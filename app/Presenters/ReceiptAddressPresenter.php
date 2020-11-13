<?php

namespace App\Presenters;

use App\Transformers\ReceiptAddressTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ReceiptAddressPresenter.
 *
 * @package namespace App\Presenters;
 */
class ReceiptAddressPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ReceiptAddressTransformer();
    }
}
