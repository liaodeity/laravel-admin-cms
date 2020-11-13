<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/4
 */

namespace App\Http\Controllers\Member;


use App\Entities\Agent;
use App\Entities\Bill;
use App\Entities\Log;
use App\Entities\Member;
use App\Entities\MemberAgent;
use App\Entities\Picture;
use App\Entities\Product;
use App\Entities\Region;
use App\Entities\WxAccount;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WxBaseController;
use App\Repositories\MemberRepositoryEloquent;
use App\Services\MapService;
use App\Services\ScanProductService;
use App\Services\WxAccountService;
use App\Validators\MemberValidator;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;

class MemberController extends WxBaseController
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var MemberRepositoryEloquent
     */
    private $repository;
    /**
     * @var MemberValidator
     */
    private $validator;
    /**
     * @var ScanProductService
     */
    private $scanProductService;

    public function __construct(Request $request, MemberRepositoryEloquent $repository, MemberValidator $validator)
    {
        parent::__construct();
        $this->request    = $request;
        $this->repository = $repository;
        $this->validator  = $validator;

    }

    //注册
    public function reg()
    {
        $response = $this->oauth($this->request->fullUrl());
        if ($response !== true) {
            return $response;//跳转到授权
        }

        $isEnableReg = $this->request->isEnableReg ?? 0;
        $agentID     = $this->request->agentID ?? 0;//代理商名称
        $memberID    = $this->request->memberID ?? 0;//推荐人会员
        if (empty($agentID) && empty($memberID)) {
            abort(403, '无法访问，请正确扫描推广二维码');
        }

        $referrer_member_id = $memberID;
        $referrer_name      = '';//推荐人

        $memberAgent = new MemberAgent();
        $agent       = new Agent();
        if ($memberID) {
            $referrerMember = Member::find($memberID);
            if ($referrerMember) {
                $referrer_name = $referrerMember->real_name ?? '';
                $memberAgent   = $referrerMember->agents()->get();//继承推荐人所有代理商
                //判断时候所有代理商都不允许发展下线，如不允许提示错误
                $not_allow = true;
                foreach ($memberAgent as $item) {
                    if ($item->is_allow_subordinate == 1) {
                        $not_allow = false;
                        break;
                    }
                }
                if ($not_allow) {
                    abort(403, '会员推广码不允许发展下线，无法注册');
                }
            }
        }
        //        dd($agentID);
        if ($agentID) {
            $agent = Agent::find($agentID);
        }
        if ($this->request->wantsJson()) {
            try {
                \Illuminate\Support\Facades\Log::info('接受参数', $this->request->all());
                if ($isEnableReg != 1) {
                    //如果存在已注册过，基本资料无需填写
                    $this->validator->with($this->request->input('Member'))->passesOrFail(MemberValidator::RULE_REG_MEMBER);
                }

                $memberID = $this->repository->regMember($this->request->all());
                $arr      = [
                    'message' => '注册成功，正在跳转...',
                    'url'     => url('member/reg-success')
                ];
                $account  = [
                    'account_id'   => $memberID,
                    'account_type' => Member::class,
                    'openid'       => $this->getOpenID()
                ];
                $this->wxAccountService->createOrUpdateAccount($account);

                return response()->json($arr);
            } catch (\ErrorException $e) {
                $arr = [
                    'error'   => true,
                    'message' => $e->getMessage()
                ];

                return response()->json($arr);
            } catch (ValidatorException $e) {
                if ($this->request->wantsJson()) {
                    return response()->json([
                        'error'   => true,
                        'message' => $e->getMessageBag()->first(),
                    ]);
                }

                return redirect()->back()->withErrors($e->getMessageBag()->first())->withInput();
            }

        }

        //是否存在已经审核通过的会员，无需填写基本资料
        $isEnableReg = 0;
        $member      = new Member();//本人信息
        $wxJsConfig  = $this->getJsSDKJson(['openLocation', 'getLocation', 'uploadImage', 'chooseImage']);
        try {
            $openID    = $this->getOpenID();
            $wxAccount = $this->wxAccountService->getAccountToOpenID($openID, Member::class);
            if (isset($wxAccount['account_type']) && $wxAccount['account_type'] == Member::class) {

                $info = Member::find($wxAccount['account_id']);//已存在绑定信息，获取用于填充表单
                if ($info) {
                    $member = $info;
                }
            }
            if (isset($member->status) && $member->status == Member::STATUS_ENABLE) {
                $isEnableReg = 1;
                //已成功注册
                foreach ($member->agents as $item) {
                    if ($item->agent_id == $agentID && $item->sp_status == $member->status) {
                        //已存在机构
                        return redirect(url('member/reg-success?type=1'));
                    }
                }
            } elseif (isset($member->status) && $member->status == Member::STATUS_PENDING) {
                //待审核中
                foreach ($member->agents as $item) {
                    if ($item->agent_id == $agentID && $item->sp_status == $member->status) {
                        //已存在机构
                        return redirect(url('member/reg-success?type=3'));
                    }
                }
            } elseif (isset($member->status) && $member->status == Member::STATUS_DISABLE) {
                //已禁用
                foreach ($member->agents as $item) {
                    if ($item->agent_id == $agentID && $item->sp_status == $member->status) {
                        //已存在机构
                        return redirect(url('member/reg-success?type=4'));
                    }
                }
            }
        } catch (\ErrorException $e) {
            abort(403, $e->getMessage());
        }

        //
        $region[] = Region::getRegionNameStrArr($member->native_region_id ?? 0);
        $region[] = Region::getRegionNameStrArr($member->resident_region_id ?? 0);
//        dd($region);


        return view('member.reg', compact('wxAccount', 'wxJsConfig', 'member',
            'agent', 'memberAgent', 'referrer_name', 'referrer_member_id',
            'isEnableReg',
            'region'
        ));
    }

    /**
     * 注册成功
     * add by gui
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function regSuccess()
    {
        $member = new Member();
        $type   = $this->request->type ?? 0;

        return view('member.reg_success', compact('member', 'type'));
    }

    //客户产品查看手册
    public function productManual($productID)
    {
        //$productID = $this->request->product_id ?? 0;
        $product = Product::find($productID);
        if (empty($product)) {
            abort(403, '产品手册不存在');
        }

        return view('member.product_manual', compact('product'));
    }

    //会员扫描失败
    public function scanFail()
    {
        $member  = new Member();
        $message = $this->request->message ?? '';

        return view('member.scan_fail', compact('member', 'message'));
    }

    /**
     * 会员扫码成功 add by gui
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCheckBill()
    {
        try {
            $member   = new Member();
            $response = $this->oauth($this->request->fullUrl());
            if ($response !== true) {
                return $response;//跳转到授权
            }

            $qrcode_key               = $this->request->qrcode_key ?? '';
            $lat                      = $this->request->lat ?? '';
            $lng                      = $this->request->lng ?? '';
            $this->scanProductService = new ScanProductService();
            $region_name              = '';
            $openID                   = $this->getOpenID();
            $qrcode_no                = $this->scanProductService->getQrCodeNoToKey($qrcode_key);
            $this->scanProductService->setQrcodeNo($qrcode_no);
            $this->scanProductService->setOpenID($openID);
            $productID   = $this->scanProductService->getProductID();
            $memberID    = $this->scanProductService->getMemberID();
            $region_name = $this->scanProductService->getRegionName();
            $agent_name  = $this->scanProductService->getAgentName();
            //
            $this->scanProductService->checkAuth();
            $billID = $this->scanProductService->receiveBill($lat, $lng);
            $bill   = Bill::find($billID);
            $arr    = [
                'bill_at'     => $bill->bill_at ?? '',
                'bill_amount' => $bill->amount ?? 0
            ];

            return response()->json($arr);
        } catch (\ErrorException $e) {
            $this->scanProductService->logs($e->getMessage());//记录扫码错误日志
            $message = $e->getMessage();
            $arr     = [
                'error'   => true,
                'message' => $message,
                'url'     => url('member/scan-fail') . '?message=' . $message
            ];

            return response()->json($arr);
        }
    }

    //客户扫码产品
    public function productScan()
    {
        $member    = new Member();
        $productID = $this->request->product_id ?? 0;
        $qrcodeNo  = $this->request->qrcode_no ?? 0;
        $product   = Product::find($productID);
        if (empty($product)) {
            abort(403, '产品手册不存在');
        }
        $scanProductService = new ScanProductService();
        $scan_number        = 0;
        try {
            $response = $this->oauth($this->request->fullUrl());
            if ($response !== true) {
                return $response;//跳转到授权
            }

            $scanProductService->setQrcodeNo($qrcodeNo);
            $scanProductService->setOpenID($this->getOpenID());
            $scan_number     = $scanProductService->getLogsScanNum('客户验证扫码');
            $scan_first_time = $scanProductService->getLogsFirstDate('客户验证扫码');
            $scan_first_date = '';
            if ($scan_first_time) {
                $scan_first_date = date('Y-m-d', strtotime($scan_first_time));
            }

            $wxJsConfig = $this->getJsSDKJson(['openLocation', 'getLocation', 'uploadImage', 'chooseImage']);
            return view('member.product_scan', compact('wxJsConfig', 'qrcodeNo', 'member', 'product', 'scan_number', 'scan_first_date'));
        } catch (\ErrorException $e) {
            abort(403, $e->getMessage());
        }
    }
    //检查商品扫码情况
    public function getCheckProductScan()
    {
        $qrcodeNo = $this->request->qrcode_no ?? 0;
        $lat      = $this->request->lat ?? 0;
        $lng      = $this->request->lng ?? 0;

        $scanProductService = new ScanProductService();
        $MapService         = new MapService();
        $scan_number        = 0;
        try {
            $response = $this->oauth($this->request->fullUrl());
            if ($response !== true) {
                return $response;//跳转到授权
            }

            $scanProductService->setQrcodeNo($qrcodeNo);
            $scanProductService->setOpenID($this->getOpenID());
            $address = '';
            if ($lat && $lng) {
                $address = $MapService->coordinateToAddress($lat, $lng, 'gcj02');
                $address = $address . '（' . $lat . ',' . $lng . '）';
            }

            $content = '扫码地址：' . $address . '，扫码时间：' . date('Y-m-d H:i:s');
            $scanProductService->logs('客户验证扫码', $content);
            $scan_number     = $scanProductService->getLogsScanNum('客户验证扫码');
            $scan_first_time = $scanProductService->getLogsFirstDate('客户验证扫码');
            $scan_first_date = '';
            if ($scan_first_time) {
                $scan_first_date = date('Y-m-d', strtotime($scan_first_time));
            }
            $arr = [
                'message'         => '获取成功',
                'scan_number'     => $scan_number,
                'scan_first_date' => $scan_first_date
            ];
            return response()->json($arr);
        } catch (\ErrorException $e) {
            return ajax_error_message($e->getMessage());
        }
    }

    /**
     * 删除图片 add by gui
     * @param $pictureID
     * @return \Illuminate\Http\JsonResponse
     */
    public function delPic($pictureID)
    {
        try {
            $Picture = new Picture();
            $Picture->deletePicture($pictureID);
            $arr = [
                'message' => '删除成功'
            ];

            return response()->json($arr);
        } catch (\ErrorException $e) {
            $arr = [
                'error'   => true,
                'message' => $e->getMessage()
            ];

            return response()->json($arr);
        }

    }
    //上传图片，获取微信图片到本地
    public function uploadMedia($mediaID)
    {
        $stream = $this->officialAccount->media->get($mediaID);
        if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            // 以内容 md5 为文件名存到本地
            try {
                $dir       = 'upload/wx-media/' . date('Ymd');
                $filename  = $stream->save($dir);
                $Picture   = new Picture();
                $path_file = $dir . '/' . $filename;
                $pictureID = $Picture->addPicture($path_file, '', Picture::TEMP_STATUS);

                $result = [
                    'url'       => asset($path_file),
                    'pictureID' => $pictureID
                ];
                $arr    = [
                    'message' => '获取文件成功',
                    'result'  => $result
                ];

                return response()->json($arr);

            } catch (InvalidArgumentException $e) {
                $message = $e->getMessage();
            } catch (RuntimeException $e) {
                $message = $e->getMessage();
            } catch (\ErrorException $e) {
                $message = $e->getMessage();
            }
            // 自定义文件名，不需要带后缀
            //$stream->saveAs('保存目录', '文件名');
        } else {
            $message = '没有获取到上传文件';
        }
        $arr = [
            'error'   => true,
            'message' => $message
        ];
        if ($e)
            \Illuminate\Support\Facades\Log::info($e);
        return response()->json($arr);
    }
    //生日页面，备用
    public function birthday()
    {
        $response = $this->oauth($this->request->fullUrl());
        if ($response !== true) {
            return $response;//跳转到授权
        }
        $openID           = $this->getOpenID();
        $WxAccountService = new WxAccountService(new WxAccount());
        try {
            $wxAccount = $WxAccountService->getAccountToOpenID($openID, Member::class);
            if (!isset($wxAccount->account_id)) {
                throw new \ErrorException('获取用户信息失败');
            }
            $memberID = $wxAccount->account_id ?? 0;
            if (empty($memberID)) {
                $memberID = $this->request->member_id ?? 0;
            }
            $member = Member::find($memberID);

            return view('member.birthday', compact('member'));

        } catch (\ErrorException $e) {
            abort(403, $e->getMessage());
        }
    }
}
