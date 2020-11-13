<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\OrderSale;

/**
 * Class OrderSaleTransformer.
 *
 * @package namespace App\Transformers;
 */
class OrderSaleTransformer extends TransformerAbstract
{
    /**
     * Transform the OrderSale entity.
     *
     * @param \App\Entities\OrderSale $model
     *
     * @return array
     */
    public function transform(OrderSale $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
