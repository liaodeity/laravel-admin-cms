<?php

namespace App\Presenters;

use App\Entities\Product;
use App\Transformers\ProductTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ProductPresenter.
 *
 * @package namespace App\Presenters;
 */
class ProductPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ProductTransformer();
    }

    public function showName($id)
    {
        $info = Product::find($id);
        return $info->title ?? '';
    }
}
