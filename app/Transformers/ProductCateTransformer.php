<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\ProductCate;

/**
 * Class ProductCateTransformer.
 *
 * @package namespace App\Transformers;
 */
class ProductCateTransformer extends TransformerAbstract
{
    /**
     * Transform the ProductCate entity.
     *
     * @param \App\Entities\ProductCate $model
     *
     * @return array
     */
    public function transform(ProductCate $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
