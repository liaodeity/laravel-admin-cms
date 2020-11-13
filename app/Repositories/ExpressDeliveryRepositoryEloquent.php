<?php

namespace App\Repositories;

use App\Entities\ExpressDeliveryInfo;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ExpressDeliveryRepository;
use App\Entities\ExpressDelivery;
use App\Validators\ExpressDeliveryValidator;

/**
 * Class ExpressDeliveryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ExpressDeliveryRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ExpressDelivery::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ExpressDeliveryValidator::class;
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
        $count = ExpressDeliveryInfo::where('delivery_id',$id)->count();
        if($count){
            return false;
        }
        return true;
    }
}
