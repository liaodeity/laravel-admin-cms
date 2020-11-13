<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Member;

/**
 * Class MemberTransformer.
 *
 * @package namespace App\Transformers;
 */
class MemberTransformer extends TransformerAbstract
{
    /**
     * Transform the Member entity.
     *
     * @param \App\Entities\Member $model
     *
     * @return array
     */
    public function transform(Member $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
