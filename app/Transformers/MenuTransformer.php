<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Menu;

/**
 * Class MenuTransformer.
 *
 * @package namespace App\Transformers;
 */
class MenuTransformer extends TransformerAbstract
{
    /**
     * Transform the Menu entity.
     *
     * @param \App\Entities\Menu $model
     *
     * @return array
     */
    public function transform(Menu $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
