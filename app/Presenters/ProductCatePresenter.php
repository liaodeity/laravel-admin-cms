<?php

namespace App\Presenters;

use App\Transformers\ProductCateTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ProductCatePresenter.
 *
 * @package namespace App\Presenters;
 */
class ProductCatePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ProductCateTransformer();
    }
}
