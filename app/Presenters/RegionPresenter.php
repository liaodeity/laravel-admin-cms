<?php

namespace App\Presenters;

use App\Entities\Region;
use App\Transformers\RegionTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class RegionPresenter.
 *
 * @package namespace App\Presenters;
 */
class RegionPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new RegionTransformer();
    }

    public function showName ($id)
    {
        $M = new Region();
        $name = $M->getLevelName ($id);

        return $name;
    }
}
