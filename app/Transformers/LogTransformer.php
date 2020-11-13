<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Log;

/**
 * Class LogTransformer.
 *
 * @package namespace App\Transformers;
 */
class LogTransformer extends TransformerAbstract
{
    /**
     * Transform the Log entity.
     *
     * @param \App\Entities\Log $model
     *
     * @return array
     */
    public function transform(Log $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
