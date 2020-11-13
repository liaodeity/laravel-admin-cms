<?php

namespace App\Presenters;

use App\Transformers\LogTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LogPresenter.
 *
 * @package namespace App\Presenters;
 */
class LogPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new LogTransformer();
    }
}
