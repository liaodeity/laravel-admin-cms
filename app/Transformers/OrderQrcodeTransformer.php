<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\OrderQrcode;

/**
 * Class OrderQrcodeTransformer.
 *
 * @package namespace App\Transformers;
 */
class OrderQrcodeTransformer extends TransformerAbstract
{
    /**
     * Transform the OrderQrcode entity.
     *
     * @param \App\Entities\OrderQrcode $model
     *
     * @return array
     */
    public function transform(OrderQrcode $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
