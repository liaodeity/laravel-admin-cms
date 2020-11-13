<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\ExpressDelivery;

/**
 * Class ExpressDeliveryTransformer.
 *
 * @package namespace App\Transformers;
 */
class ExpressDeliveryTransformer extends TransformerAbstract
{
    /**
     * Transform the ExpressDelivery entity.
     *
     * @param \App\Entities\ExpressDelivery $model
     *
     * @return array
     */
    public function transform(ExpressDelivery $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
