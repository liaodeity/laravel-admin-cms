<?php

namespace App\Repositories;

use App\Entities\OrderProduct;
use App\Entities\Product;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductCateRepository;
use App\Entities\ProductCate;
use App\Validators\ProductCateValidator;

/**
 * Class ProductCateRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductCateRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductCate::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ProductCateValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function allowDelete($id)
    {
        $count = Product::where('cate_id', $id)->count();
        if($count){
            return false;
        }

        return true;
    }

}
