<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Region;

/**
 * Class RegionTransformer.
 *
 * @package namespace App\Transformers;
 */
class RegionTransformer extends TransformerAbstract
{
    /**
     * Transform the Region entity.
     *
     * @param \App\Entities\Region $model
     *
     * @return array
     */
    public function transform(Region $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
