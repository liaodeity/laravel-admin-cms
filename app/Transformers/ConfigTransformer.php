<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Config;

/**
 * Class ConfigTransformer.
 *
 * @package namespace App\Transformers;
 */
class ConfigTransformer extends TransformerAbstract
{
    /**
     * Transform the Config entity.
     *
     * @param \App\Entities\Config $model
     *
     * @return array
     */
    public function transform(Config $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
