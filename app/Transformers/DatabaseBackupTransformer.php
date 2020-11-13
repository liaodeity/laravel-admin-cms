<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\DatabaseBackup;

/**
 * Class DatabaseBackupTransformer.
 *
 * @package namespace App\Transformers;
 */
class DatabaseBackupTransformer extends TransformerAbstract
{
    /**
     * Transform the DatabaseBackup entity.
     *
     * @param \App\Entities\DatabaseBackup $model
     *
     * @return array
     */
    public function transform(DatabaseBackup $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
