<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Agent;

/**
 * Class AgentTransformer.
 *
 * @package namespace App\Transformers;
 */
class AgentTransformer extends TransformerAbstract
{
    /**
     * Transform the Agent entity.
     *
     * @param \App\Entities\Agent $model
     *
     * @return array
     */
    public function transform(Agent $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
