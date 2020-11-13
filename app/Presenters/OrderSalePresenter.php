<?php

namespace App\Presenters;

use App\Transformers\OrderSaleTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class OrderSalePresenter.
 *
 * @package namespace App\Presenters;
 */
class OrderSalePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new OrderSaleTransformer();
    }
}
