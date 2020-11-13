<?php

namespace App\Presenters;

use App\Transformers\MenuTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class MenuPresenter.
 *
 * @package namespace App\Presenters;
 */
class MenuPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new MenuTransformer();
    }
}
