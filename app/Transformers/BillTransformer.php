<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Bill;

/**
 * Class BillTransformer.
 *
 * @package namespace App\Transformers;
 */
class BillTransformer extends TransformerAbstract
{
    /**
     * Transform the Bill entity.
     *
     * @param \App\Entities\Bill $model
     *
     * @return array
     */
    public function transform(Bill $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
