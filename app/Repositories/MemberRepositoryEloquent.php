<?php

namespace App\Repositories;

use App\Entities\Bill;
use App\Entities\Log;
use App\Entities\MemberAgent;
use App\Entities\Picture;
use App\Entities\SerialNumber;
use App\Services\MapService;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\MemberRepository;
use App\Entities\Member;
use App\Validators\MemberValidator;

/**
 * Class MemberRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MemberRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model ()
    {
        return Member::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator ()
    {

        return MemberValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot ()
    {
        $this->pushCriteria (app (RequestCriteria::class));
    }

    /**
     * 会员注册 add by gui
     * @param $input
     * @return mixed
     * @throws \ErrorException
     */
    public function regMember ($input)
    {
        $inputAgent     = $input['MemberAgent'];
        $inputMember    = $input['Member'];
        $agentIDArr     = $inputAgent['agent_id'] ?? [];
        $inputPicture   = $input['Picture'] ?? [];
        $pictureArr     = $inputPicture['id'] ?? [];
        $input_agent_id = $input['agent_id'] ?? 0;
        $lat            = $inputAgent['lat'] ?? '';
        $lng            = $inputAgent['lng'] ?? '';
        if (empty($inputMember['birthday'])) {
            unset($inputMember['birthday']);
        }
        input_default ($inputMember);
        if (empty($lat) || empty($lng)) {
            throw new \ErrorException('无法获取位置信息，无法注册，请开启位置获取');
        }
        if (empty($agentIDArr)) {
            throw new \ErrorException('代理商机构不存在');
        }
        if (empty($pictureArr)) {
            throw new \ErrorException('请拍摄现场图片');
        }
        if (count ($pictureArr) < 2) {
            throw new \ErrorException('现场图片必须2张或以上');
        }
        $mapService = new MapService();
        $agentIDArr = array_unique ($agentIDArr);
        $memberID   = $inputMember['member_id'] ?? 0;
        if ($memberID) {
            //修改
            $member = Member::find ($memberID);

        } else {
            $member = new Member();

            $inputMember['native_region_id']   = empty($inputMember['native_region_id']) ? 0 : $inputMember['native_region_id'];
            $inputMember['resident_region_id'] = empty($inputMember['resident_region_id']) ? 0 : $inputMember['resident_region_id'];
            $inputMember['status']             = Member::STATUS_PENDING;
            $inputMember['reg_date']           = date ('Y-m-d');
        }
        if (isset($member->status) && $member->status == Member::STATUS_ENABLE) {
            //已通过的资料
        } else {


        }
        if (isset($inputMember['wx_account'])) {
            //微信账号不允许有空格
            $inputMember['wx_account'] = trim ($inputMember['wx_account']);
            $inputMember['wx_account'] = str_replace (' ', '', $inputMember['wx_account']);
        }
        $member->fill ($inputMember);
        $ret = $member->save ();
        if (!$ret) {
            throw new \ErrorException('注册会员失败');
        }
        //更新编号
        SerialNumber::authUpdateToSourceNo (Member::class, $member->id, 'member_no');
        $Picture = new Picture();
        foreach ($agentIDArr as $agent_id) {
            if (empty($agent_id)) {
                continue;
            }

            $memberAgent = MemberAgent::where ('member_id', $member->id)->where ('agent_id', $agent_id)->first ();
            if (empty($memberAgent)) {
                $memberAgent = new MemberAgent();
            }else{
                if(object_get ($memberAgent,'sp_status') == Member::STATUS_ENABLE){
                    //会员已通过，不在更新
                    continue;
                }
            }
            $insArr              = $inputAgent;
            $insArr['agent_id']  = $agent_id;
            $insArr['member_id'] = $member->id;
            //坐标地址
            if ($lat && $lng) {
                //转换
                $loc           = $mapService->translate ($lat, $lng);
                $insArr['lat'] = $loc['lat'] ?? $lat;
                $insArr['lng'] = $loc['lng'] ?? $lng;
                //地址
                $address               = $mapService->coordinateToAddress ($insArr['lat'], $insArr['lng']);
                $insArr['loc_address'] = $address;
            }
            $insArr['sp_status'] = Member::STATUS_PENDING;//待审核
            $memberAgent->fill ($insArr);
            $ret = $memberAgent->save ();
            if (!$ret) {
                throw new \ErrorException('更新机构会员信息失败');
            }

            // 更新上传图片
            $memberAgentID = $memberAgent->id;
            // 需要删除之前的图片，保留最后一次注册图片
            Picture::where ('picture_id', $memberAgentID)->where ('picture_type', MemberAgent::class)->delete ();
            foreach ($pictureArr as $picID) {
                $info = $Picture->find ($picID);
                if ($info) {
                    $Picture->addPicture ($info->path, $info->title, 1, $memberAgentID, MemberAgent::class);
                }
            }

        }

        Log::createLog (Log::ADD_TYPE, '提交注册会员成功');

        return $member->id;
    }

    /**
     * 保存会员机构[审核] add by gui
     * @param $memberID
     * @param $input
     * @return bool
     * @throws \ErrorException
     */
    public function saveMemberAgent ($memberID, $input)
    {
        $inputMemberAgent = $input['MemberAgent'] ?? [];
        if (empty($inputMemberAgent)) {
            throw new \ErrorException('参数接受失败');
        }
        DB::beginTransaction ();
        $member      = Member::find ($memberID);
        $memberAgent = MemberAgent::where ('member_id', $memberID)->where ('agent_id', $inputMemberAgent['agent_id'])->first ();
        if ($memberAgent) {
            $inputMemberAgent['is_allow_subordinate'] = $inputMemberAgent['is_allow_subordinate'] ?? 0;
            $memberAgent->fill ($inputMemberAgent);
            $ret = $memberAgent->save ();
            if ($ret) {
                if (isset($inputMemberAgent['sp_status']) && $inputMemberAgent['sp_status'] != $member->status) {
                    $member->status = $inputMemberAgent['sp_status'];
                    $ret            = $member->save ();
                    if (!$ret) {
                        throw new \ErrorException('更新会员资料失败');
                    }
                }
                Log::createLog (Log::EDIT_TYPE, '保存会员' . $member->member_no . '记录');
            } else {
                throw new \ErrorException('更新机构会员信息失败');
            }

        }
        DB::commit ();
        return true;
    }

    /**
     * 判断是否允许删除
     * add by gui
     * @param $memberID
     */
    public function allowDelete ($memberID)
    {
        $count = Bill::where ('member_id', $memberID)->count ();
        if ($count) {
            return false;
        }


        return true;
    }
}
