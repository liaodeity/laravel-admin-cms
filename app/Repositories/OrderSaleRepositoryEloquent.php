<?php

namespace App\Repositories;

use App\Entities\ExpressDelivery;
use App\Entities\ExpressDeliveryInfo;
use App\Entities\Order;
use App\Entities\OrderProduct;
use App\Entities\OrderSaleLog;
use App\Entities\OrderSaleProduct;
use App\Entities\SerialNumber;
use App\Services\OrderQrCodeService;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderSaleRepository;
use App\Entities\OrderSale;
use App\Validators\OrderSaleValidator;

/**
 * Class OrderSaleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderSaleRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model ()
    {
        return OrderSale::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator ()
    {

        return OrderSaleValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot ()
    {
        $this->pushCriteria (app (RequestCriteria::class));
    }

    /**
     * 保存售后订单处理 add by gui
     * @param $saleID
     * @param $input
     * @throws \ErrorException
     */
    public function saveOrderSaleDeal ($saleID, $input)
    {
        $inputOrderSale = $input['OrderSale'];
        $inputProduct   = $input['OrderSaleProduct'];
        $inputDelivery  = $input['ExpressDeliveryInfo'];
        $status         = $inputOrderSale['status'] ?? 0;
        $process_desc   = $inputOrderSale['process_desc'] ?? '';
        $delivery_id    = $inputDelivery['delivery_id'] ?? 0;
        $delivery_no    = $inputDelivery['delivery_no'] ?? '';
        if (empty($process_desc)) {
            throw new \ErrorException('请填写处理说明');
        }
        if (empty($status)) {
            throw new \ErrorException('请选择处理状态');
        }
        $content = $process_desc;
        DB::beginTransaction ();
        //产品明细
        if (is_array ($inputProduct)) {
            foreach ($inputProduct as $item) {
                $id                        = $item['id'] ?? 0;
                $M                         = OrderSaleProduct::where ('order_sale_id', $saleID)->where ('order_product_id', $id)->first ();
                $orderProduct              = OrderProduct::find ($id);
                $item['is_develop_member'] = $orderProduct->is_develop_member ?? 0;
                $item['is_put_card']       = $orderProduct->is_put_card ?? 0;
                if ($M) {
                    unset($item['id']);
                    $M->fill ($item);
                    $ret = $M->save ();
                } else {
                    unset($item['id']);
                    $item['order_sale_id']    = $saleID;
                    $item['order_product_id'] = $id;
                    $ret                      = OrderSaleProduct::create ($item);
                }
                if (!$ret) {
                    throw new \ErrorException('保存售后订单产品明细失败');
                }
            }
        }
//        快递信息
        $send_back_delivery_id = 0;
        if ($status == OrderSale::COMPLETE_STATUS) {
            //完成
//            if (empty($delivery_id)) {
//                throw new \ErrorException('请选择回寄快递公司');
//            }
//            if (empty($delivery_no)) {
//                throw new \ErrorException('请输入回寄快递单号');
//            }
            if ($delivery_id && $delivery_no) {
                $delivery              = ExpressDelivery::find ($delivery_id);
                $send_back_delivery_id = ExpressDeliveryInfo::createInfo ($delivery_id, $delivery_no, OrderSale::class, $saleID);
                if (!$send_back_delivery_id) {
                    throw new \ErrorException('回寄快递信息更新失败');
                }
                $content .= "；快递公司：" . $delivery->name . "；快递编号：" . $delivery_no;
            }
        }
        //售后订单信息
        $orderSale                        = OrderSale::find ($saleID);
        $orderSale->process_desc          = $process_desc;
        $orderSale->status                = $status;
        $orderSale->send_back_delivery_id = $send_back_delivery_id;
        $ret                              = $orderSale->save ();
        OrderSaleLog::createLog ($saleID, '进行了处理售后订单操作；记录说明：' . $content, '修改状态为：' . $orderSale->statusItem ($status));
        DB::commit ();
        return $ret ? true : false;
    }

    /**
     * 新增售后
     * add by gui
     * @param null $orderID
     * @param $input
     * @throws \ErrorException
     */
    public function createOrderSale ($orderID, $input)
    {
        $inputSale       = $input['OrderSale'] ?? [];
        $inputDelivery   = $input['ExpressDeliveryInfo'] ?? [];
        $delivery_id     = $inputDelivery['delivery_id'] ?? 0;
        $apply_desc      = $inputSale['apply_desc'] ?? '';
        $delivery_no     = $inputDelivery['delivery_no'] ?? '';
        $is_return_goods = $inputSale['is_return_goods'] ?? 0;
        if (is_null ($orderID)) {
            $orderID = $inputSale['order_id'];
        }
        DB::beginTransaction ();
        $content = $apply_desc;
        if (empty($apply_desc)) {
            throw new \ErrorException('请输入售后的原因及说明');
        }
        if ($is_return_goods == 1) {
            //有退换
            if (empty($delivery_id)) {
                throw new \ErrorException('请选择邮寄的快递公司');
            }
            if (empty($delivery_no)) {
                throw  new \ErrorException('请输入快递单号');
            }
        }
        $status    = OrderSale::NO_DEAL;
        $insArr    = [
            'order_id'              => (int)$inputSale['order_id'],
            'agent_id'              => get_agent_id (),
            'apply_sale_at'         => date ('Y-m-d H:i:s'),
            'status'                => $status,
            'sale_no'               => '',
            'apply_desc'            => $apply_desc,
            'sale_delivery_id'      => 0,
            'send_back_delivery_id' => 0,
            'is_return_goods'       => $is_return_goods,
        ];
        $orderSale = OrderSale::create ($insArr);
        if (!$orderSale) {
            throw new \ErrorException('新增售后记录失败');
        }
        $saleID  = $orderSale->id;
        $sale_no = SerialNumber::autoNumber (OrderSale::class, $saleID);

        if ($is_return_goods == 1) {
            //跟编号跟寄回信息
            $sale_delivery_id            = ExpressDeliveryInfo::createInfo ($delivery_id, $delivery_no, OrderSale::class, $saleID);
            $orderSale->sale_delivery_id = $sale_delivery_id;
            $delivery                    = ExpressDelivery::find ($delivery_id);
            $content                     .= "；快递公司：" . $delivery->name . "；快递编号：" . $delivery_no;
        }

        $orderSale->sale_no = $sale_no;
        $ret                = $orderSale->save ();
        if (!$ret) {
            throw  new \ErrorException('更新售后信息失败');
        }


        OrderSaleLog::createLog ($saleID, '新增售后记录；记录说明：' . $content, '修改状态为：' . $orderSale->statusItem ($status));
        DB::commit ();

        return $ret ? true : fasle;
    }

    //    保存产品条目信息
    protected function saveProductItem ($saleID, $products)
    {
        foreach ($products as $item) {
            $id = $item['id'] ?? 0;
            $M  = OrderSaleProduct::where ('order_sale_id', $saleID)->where ('order_product_id', $id)->first ();

            if ($M) {
                unset($item['id']);
                $M->fill ($item);
                $ret = $M->save ();
            } else {
                unset($item['id']);
                $item['order_sale_id']    = $saleID;
                $item['order_product_id'] = $id;
                $ret                      = OrderSaleProduct::create ($item);
            }
            if (!$ret) {
                throw new \ErrorException('保存售后订单产品明细失败');
            }
        }

        //foreach ($products as $item) {
        //    $id = isset($item['id']) ? $item['id'] : 0;
        //    if ($id) {
        //        $M = OrderProduct::find($id);
        //        $M->fill($item);
        //        $ret = $M->save();
        //    } else {
        //        unset($item['id']);
        //        $item['order_id'] = $orderID;
        //        $ret              = OrderProduct::create($item);
        //    }
        //    if (!$ret) {
        //        throw new \ErrorException('订单产品明细保存失败');
        //    }
        //}
        return true;
    }

    /**
     * 生成产品二维码
     * add by gui
     * @param $orderSaleID
     * @param $input
     * @return bool
     * @throws \ErrorException
     */
    public function saveGenerateQrcode ($orderSaleID, $input)
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
        $this->saveProductItem ($orderSaleID, $inputProduct);

        return $service->saveGenerateQrCodeToSale ($orderSaleID, $input);
    }
}
