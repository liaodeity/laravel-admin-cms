<?php

namespace App\Presenters;

use App\Transformers\BillTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BillPresenter.
 *
 * @package namespace App\Presenters;
 */
class BillPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BillTransformer();
    }
}
