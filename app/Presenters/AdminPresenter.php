<?php

namespace App\Presenters;

use App\Repositories\AdminRepositoryEloquent;
use App\Transformers\AdminTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class AdminPresenter.
 * @package namespace App\Presenters;
 */
class AdminPresenter extends FractalPresenter
{
    /**
     * Transformer
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new AdminTransformer();
    }
    
    public function getRoleNameString($list)
    {
        //$repositoryEloquent = new AdminRepositoryEloquent();
        //
        //$list = $repositoryEloquent->getRoleList($admin_id);
        $arr  = [];
        foreach ($list as $item) {
            if ($item->checked) $arr[] = $item->title;
        }
        
        return empty($arr) ? '' : implode('、', $arr);
    }
}
