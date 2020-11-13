<?php

namespace App\Presenters;

use App\Transformers\AgentTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class AgentPresenter.
 *
 * @package namespace App\Presenters;
 */
class AgentPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new AgentTransformer();
    }
}
