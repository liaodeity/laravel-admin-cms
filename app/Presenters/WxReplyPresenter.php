<?php

namespace App\Presenters;

use App\Transformers\WxReplyTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class WxReplyPresenter.
 *
 * @package namespace App\Presenters;
 */
class WxReplyPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new WxReplyTransformer();
    }
}
