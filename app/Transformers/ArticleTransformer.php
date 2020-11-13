<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Article;

/**
 * Class ArticleTransformer.
 *
 * @package namespace App\Transformers;
 */
class ArticleTransformer extends TransformerAbstract
{
    /**
     * Transform the Article entity.
     *
     * @param \App\Entities\Article $model
     *
     * @return array
     */
    public function transform(Article $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
