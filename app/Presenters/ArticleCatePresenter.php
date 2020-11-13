<?php

namespace App\Presenters;

use App\Transformers\ArticleCateTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ArticleCatePresenter.
 *
 * @package namespace App\Presenters;
 */
class ArticleCatePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ArticleCateTransformer();
    }
}
