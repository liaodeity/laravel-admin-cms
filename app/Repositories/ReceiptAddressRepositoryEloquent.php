<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ReceiptAddressRepository;
use App\Entities\ReceiptAddress;
use App\Validators\ReceiptAddressValidator;

/**
 * Class ReceiptAddressRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ReceiptAddressRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ReceiptAddress::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ReceiptAddressValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
