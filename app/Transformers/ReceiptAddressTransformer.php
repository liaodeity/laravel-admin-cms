<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\ReceiptAddress;

/**
 * Class ReceiptAddressTransformer.
 *
 * @package namespace App\Transformers;
 */
class ReceiptAddressTransformer extends TransformerAbstract
{
    /**
     * Transform the ReceiptAddress entity.
     *
     * @param \App\Entities\ReceiptAddress $model
     *
     * @return array
     */
    public function transform(ReceiptAddress $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
