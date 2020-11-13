<?php

namespace App\Repositories;

use App\Entities\OrderProduct;
use App\Entities\ProductPrice;
use ErrorException;
use League\Fractal\Resource\Primitive;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Product;
use App\Validators\ProductValidator;

/**
 * Class ProductRepositoryEloquent.
 * @package namespace App\Repositories;
 */
class ProductRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model()
    {
        return Product::class;
    }

    /**
     * Specify Validator class name
     * @return mixed
     */
    public function validator()
    {

        return ProductValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 创建商品的价格信息 add by gui
     * @param $productID
     * @param $input
     * @return bool
     * @throws ErrorException
     */
    public function createProductPrice($productID, $input)
    {
        $specifications = $input['specification'];
        $prices         = $input['price'];
        $ids            = [];
        if (empty($prices)) {
            return false;
        }
        foreach ($specifications as $key => $specification) {
            if(empty($specification)){
                throw new ErrorException('规格名称 不能为空');
            }
            $price = $prices[$key] ?? 0;
            $price = abs($price);
            if(empty($price)){
                throw new ErrorException('规格价格 不能为空');
            }
            $info  = ProductPrice::where('specification', $specification)->where('product_id', $productID)->first();
            if ($info && isset($info->id)) {
                //修改
                $ids[] = $info->id;
                $ret   = ProductPrice::where('id', $info->id)->update(['price' => $price]);
                if (!$ret) {
                    throw new ErrorException('修改价格失败');
                }
            } else {
                //新增
                $insArr = [
                    'product_id'    => $productID,
                    'specification' => $specification,
                    'price'         => $price,
                ];
                $info   = ProductPrice::create($insArr);
                if ($info && isset($info->id)) {
                    $ids[] = $info->id;
                } else {
                    throw new ErrorException('保存价格失败');
                }
            }
        }

        //清除不存在的规格
        $ret = ProductPrice::where('product_id', $productID)->whereNotIn('id', $ids)->delete();

        return $ret ? true : false;
    }

    public function allowDelete($id)
    {
        $count = OrderProduct::where('product_id', $id)->count();
        if($count){
            return false;
        }

        return true;
    }
}
