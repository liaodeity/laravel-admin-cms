<?php

namespace App\Repositories;

use App\Entities\Agent;
use App\Entities\ExpressDelivery;
use App\Entities\ExpressDeliveryInfo;
use App\Entities\Log;
use App\Entities\OrderLog;
use App\Entities\OrderProduct;
use App\Entities\OrderQrcode;
use App\Entities\ProductPrice;
use App\Entities\ReceiptAddress;
use App\Entities\SerialNumber;
use App\Exports\OrderQrcodeExport;
use App\Services\MessageNoticeService;
use App\Services\OrderQrCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderRepository;
use App\Entities\Order;
use App\Validators\OrderValidator;
use ZanySoft\Zip\Zip;

/**
 * Class OrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model ()
    {
        return Order::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator ()
    {

        return OrderValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot ()
    {
        $this->pushCriteria (app (RequestCriteria::class));
    }

//    保存产品条目信息
    protected function saveProductItem ($orderID, $products)
    {
        foreach ($products as $item) {
            $id = isset($item['id']) ? $item['id'] : 0;
            if ($id) {
                $M = OrderProduct::find ($id);
                $M->fill ($item);
                $ret = $M->save ();
            } else {
                unset($item['id']);
                $item['order_id'] = $orderID;
                $ret              = OrderProduct::create ($item);
            }
            if (!$ret) {
                throw new \ErrorException('订单产品明细保存失败');
            }
        }
        return true;
    }

    /**
     * 保存订单产品信息
     * add by gui
     * @param $orderID
     * @param $input
     * @return bool
     * @throws \ErrorException
     */
    public function saveOrderProduct ($orderID, $input)
    {
        if (empty($input)) {
            return false;
        }
        $remark = request ()->input ('Order.remark', '');
        $order  = Order::find ($orderID);
        if (!$order) {
            throw new \ErrorException('订单不存在');
        }
        if (!Order::isNoPay ($order->status)) {
            throw new \ErrorException('未付款才能审核订单');
        }
        $this->saveProductItem ($orderID, $input);

        //更新已付款未发货
        $order->status = Order::NO_DELIVERY_STATUS;
        if ($remark) {
            $order->remark = $remark;
        }
        $ret = $order->save ();
        OrderLog::createAdminLog ($orderID, '进行了审核订单操作', '修改状态为：' . $order->statusItem ($order->status));
        $this->saveOrderAmount ($orderID);

        return $ret ? true : false;
    }

    /**
     * 保存订单处理
     * add by gui
     * @param $orderID
     * @param $input
     * @return bool
     * @throws \ErrorException
     */
    public function saveOrderDeal ($orderID, $input)
    {
        DB::beginTransaction ();
        $inputOrder    = $input['Order'] ?? [];
        $inputLog      = $input['Log'] ?? [];
        $inputDelivery = $input['Delivery'] ?? [];
        $status        = $inputOrder['status'] ?? 0;
        $content       = $inputLog['content'] ?? null;
        $delivery_id   = isset($inputDelivery['delivery_id']) ? $inputDelivery['delivery_id'] : 0;
        $delivery_no   = isset($inputDelivery['delivery_no']) ? $inputDelivery['delivery_no'] : 0;
        if (empty($content) && !is_null ($content)) {
            throw new \ErrorException('记录说明不能为空');
        }
        //添加快递信息
        if ($status == Order::YES_DELIVERY_STATUS) {
            if (empty($delivery_id)) {
                throw new \ErrorException('请选择快递公司');
            }
            if (empty($delivery_no)) {
                throw new \ErrorException('请输入快递编号');
            }
        }
        $order         = Order::find ($orderID);
        $order->status = $status;
        if ($status == Order::YES_DELIVERY_STATUS) {
            //发货
            $delivery = ExpressDelivery::find ($delivery_id);

            ExpressDeliveryInfo::createInfo ($delivery_id, $delivery_no, Order::class, $orderID);

            $order->delivery_at = date ('Y-m-d H:i:s');//发货时间

            $content .= "；快递公司：" . $delivery->name . "；快递编号：" . $delivery_no;
        }
        $ret = $order->save ();
        OrderLog::createLog ($orderID, '进行了处理订单操作；记录说明：' . $content, '修改状态为：' . $order->statusItem ($status));
        DB::commit ();
        return $ret ? true : false;
    }

    /**
     * 生成产品二维码
     * add by gui
     * @param $orderID
     * @param $input
     * @return bool
     * @throws \ErrorException
     */
    public function saveGenerateQrcode ($orderID, $input)
    {

        set_time_limit (0);
        $inputProduct      = $input['OrderProduct'];
        $inputQrcode       = $input['OrderQrcode'];
        $region_name       = $inputQrcode['region_name'] ?? '';
        $quality_inspector = $inputQrcode['quality_inspector'] ?? '';
        $production_date   = $inputQrcode['production_date'] ?? date ('Y-m-d');
        $qrcodeType        = $input['qrcodeType'] ?? 'Qrcode';

        if (empty($region_name)) {
            throw new \ErrorException('销售地址 不能为空');
        }
        if (empty($quality_inspector)) {
            throw new \ErrorException('质检员 不能为空');
        }
        if (empty($production_date)) {
            throw new \ErrorException('生产日期 不能为空');
        }

        $service = new OrderQrCodeService();
        //保存生成批号
        $this->saveProductItem ($orderID, $inputProduct);

        return $service->saveGenerateQrCodeToOrder ($orderID, $input);

    }

    /**
     * 代理商下单订购
     * add by gui
     * @throws \ErrorException
     */
    public function saveAgentBuy ($orderID, $input)
    {
        $inputOrderProduct = $input['OrderProduct'] ?? [];
        $insArr            = [
            'agent_id' => get_agent_id (),
            'status'   => Order::NO_PAY_STATUS
        ];
        $number            = 0;
        foreach ($inputOrderProduct as $item) {
            $number += intval ($item['number']);
        }
        if (empty($number)) {
            throw new \ErrorException('请选择下单产品');
        }
        DB::beginTransaction ();
        if ($orderID) {
            $order = Order::find ($orderID);
        } else {
            $insArr['order_no'] = SerialNumber::autoNumber (Order::class);
            $order              = Order::create ($insArr);
            if (!$order) {
                throw new \ErrorException('创建订单失败');
            }
            $orderID = $order->id;
            SerialNumber::updateSerialID ($insArr['order_no'], $orderID);
            $this->saveConsignee ($orderID, null);//默认收货地址
            OrderLog::createLog ($orderID, '创建订单成功', '', Order::class, $orderID);
        }

        foreach ($inputOrderProduct as $item) {
            $price_id       = $item['id'];
            $number         = intval ($item['number']);
            $number         = abs ($number);
            $brokerage      = abs ($item['brokerage'] ?? 0);
            $open_brokerage = $item['open_brokerage'] ?? 0;
            $is_put_card    = $item['is_put_card'] ?? 0;
            if (empty($number)) {
                continue;//不订购数量
            }
            if ($is_put_card != 1) {
                //不放卡，无佣金
                $brokerage      = 0;
                $open_brokerage = 0;
            }
            $productPrice = ProductPrice::find ($price_id);
            $insArr       = [
                'order_id'          => $orderID,
                'product_id'        => $productPrice->product->id,
                'product_price_id'  => $price_id,
                'title'             => $productPrice->product->title,
                'standard_no'       => $productPrice->product->standard_no,
                'shelf_life'        => $productPrice->product->shelf_life,
                'specification'     => $productPrice->specification,
                'price'             => $productPrice->price,
                'number'            => $number,
                'brokerage'         => $brokerage,
                'open_brokerage'    => $open_brokerage,
                'unit'              => $productPrice->product->unit,
                'warehouse'         => $productPrice->product->warehouse,
                'is_develop_member' => $productPrice->product->is_develop_member,
                'is_put_card'       => $is_put_card,
            ];
            $info         = OrderProduct::where ('order_id', $orderID)->where ('product_price_id', $insArr['product_price_id'])->first ();
            if (empty($info)) {
                $ret = OrderProduct::create ($insArr);
            } else {
                $info->fill ($insArr);
                $ret = $info->save ();
            }

            if (!$ret) {
                throw  new \ErrorException('下单数量添加失败');
            }
        }
        $this->saveOrderAmount ($orderID);

        DB::commit ();

        return $orderID;
    }

    /**
     *
     * 保存订单收货地址，无指定将使用默认地址 add by gui
     * @param $orderID
     * @param null $receiptID
     * @return bool
     * @throws \ErrorException
     */
    public function saveConsignee ($orderID, $receiptID = null)
    {
        if (is_null ($receiptID)) {
            //获取默认收货地址
            $receipt = ReceiptAddress::where ('agent_id', get_agent_id ())
                ->where ('is_default', 1)
                ->first ();
        } else {
            $receipt = ReceiptAddress::find ($receiptID);
        }
        $order                      = Order::find ($orderID);
        $order->consignee           = $receipt->consignee ?? '';
        $order->consignee_phone     = $receipt->consignee_phone ?? '';
        $order->consignee_region_id = $receipt->region_id ?? 0;
        $order->consignee_address   = $receipt->address ?? '';
        $ret                        = $order->save ();
        if ($ret) {
            return true;
        } else {
            throw new \ErrorException('保存订单收货地址失败');
        }

    }

    /**
     * 重新计算订单总价格
     * add by gui
     * @throws \ErrorException
     */
    public function saveOrderAmount ($orderID)
    {
        $order               = Order::find ($orderID);
        $amount              = $order->productsPriceSum ();
        $order->order_amount = $amount;
        $ret                 = $order->save ();
        if ($ret) {
            return true;
        } else {
            throw new \ErrorException('订单金额计算失败');
        }
    }

    /**
     * 修改佣金设置
     * add by gui
     * @param $orderID
     * @param $input
     * @throws \ErrorException
     */
    public function updateBrokerage ($orderID, $input)
    {
        $inputProduct = $input['OrderProduct'] ?? [];

        foreach ($inputProduct as $id => $item) {
            $M                      = OrderProduct::where ('order_id', $orderID)->where ('id', $id)->first ();
            $item['open_brokerage'] = $item['open_brokerage'] ?? 0;
            $item['is_put_card']    = $item['is_put_card'] ?? 0;
            if ($item['is_put_card'] != 1) {
                //不放卡。无佣金
                $item['brokerage']      = 0;
                $item['open_brokerage'] = 0;
            }
            $M->fill ($item);
            $ret = $M->save ();
            if (!$ret) {
                throw new \ErrorException('更新失败');
            }
            //同步修改已生成二维码佣金
            $brokerage = $item['brokerage'] ?? 0;
            OrderQrcode::where ('order_product_id', $id)->update (['brokerage' => $brokerage]);
        }
        return true;
    }

    /**
     * 检查已添加的订单商品下架的情况
     * add by gui
     * @param $orderID
     * @throws \ErrorException
     */
    public function updateProductNumber ($orderID)
    {
        $order = Order::find ($orderID);
        if (Order::isNoPay ($order->status) === false) {
            //非未付款，无需检查下架情况
            return null;
        }
//        $products = OrderProduct::where('order_id', $orderID)->get();
        foreach ($order->products as $product) {
            $status = $product->product->status ?? null;
            if ($status != 1) {
                //非发布状态或已下架，删除
                OrderProduct::where ('id', $product->id)->delete ();
            }
        }
        //重新计算订单金额
        $this->saveOrderAmount ($orderID);
    }

}
