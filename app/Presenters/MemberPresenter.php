<?php

namespace App\Presenters;

use App\Transformers\MemberTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class MemberPresenter.
 *
 * @package namespace App\Presenters;
 */
class MemberPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new MemberTransformer();
    }
}
