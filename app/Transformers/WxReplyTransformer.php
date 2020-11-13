<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\WxReply;

/**
 * Class WxReplyTransformer.
 *
 * @package namespace App\Transformers;
 */
class WxReplyTransformer extends TransformerAbstract
{
    /**
     * Transform the WxReply entity.
     *
     * @param \App\Entities\WxReply $model
     *
     * @return array
     */
    public function transform(WxReply $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
