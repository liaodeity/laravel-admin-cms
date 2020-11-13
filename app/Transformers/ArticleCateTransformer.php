<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\ArticleCate;

/**
 * Class ArticleCateTransformer.
 *
 * @package namespace App\Transformers;
 */
class ArticleCateTransformer extends TransformerAbstract
{
    /**
     * Transform the ArticleCate entity.
     *
     * @param \App\Entities\ArticleCate $model
     *
     * @return array
     */
    public function transform(ArticleCate $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
