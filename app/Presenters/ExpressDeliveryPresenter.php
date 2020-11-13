<?php

namespace App\Presenters;

use App\Transformers\ExpressDeliveryTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ExpressDeliveryPresenter.
 *
 * @package namespace App\Presenters;
 */
class ExpressDeliveryPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ExpressDeliveryTransformer();
    }
}
