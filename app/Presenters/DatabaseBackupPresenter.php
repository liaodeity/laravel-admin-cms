<?php

namespace App\Presenters;

use App\Transformers\DatabaseBackupTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class DatabaseBackupPresenter.
 *
 * @package namespace App\Presenters;
 */
class DatabaseBackupPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new DatabaseBackupTransformer();
    }
}
