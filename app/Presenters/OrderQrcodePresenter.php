<?php

namespace App\Presenters;

use App\Transformers\OrderQrcodeTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class OrderQrcodePresenter.
 *
 * @package namespace App\Presenters;
 */
class OrderQrcodePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new OrderQrcodeTransformer();
    }
}
