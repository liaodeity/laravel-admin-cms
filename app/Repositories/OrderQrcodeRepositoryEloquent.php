<?php

namespace App\Repositories;

use App\Entities\Order;
use App\Entities\OrderSale;
use Illuminate\Support\Facades\Storage;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderQrcodeRepository;
use App\Entities\OrderQrcode;
use App\Validators\OrderQrcodeValidator;
use ZanySoft\Zip\Zip;

/**
 * Class OrderQrcodeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderQrcodeRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderQrcode::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return OrderQrcodeValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 获取打包zip下载地址
     * add by gui
     * @param $type [order=订单/sale=售后]
     * @param $id [订单ID/售后ID]
     * @param $qrcodeType
     * @return string
     * @throws \ErrorException
     */
    public function getDownZipPath($type, $id, $qrcodeType)
    {
        if($type == 'order'){
            $order    = Order::find($id);
            $no = $order->order_no;
        }elseif($type == 'sale'){
            $order = OrderSale::find($id);
            $no = $order->sale_no;
        }else{
            throw new \ErrorException('生成类型错误');
        }

        $file     = $qrcodeType . '/' . $no . '.zip';
        $zip_file = storage_path('app/' . $file);
        Storage::delete($file);
        $zip = Zip::create($zip_file);
        $zip->add(storage_path('app/' . $qrcodeType . '/' . $no));
        $zip->close();
        $exists = Storage::exists($file);
        if (!$exists) {
            throw new \ErrorException('二维码打包失败');
        }

        return $file;
    }
}
