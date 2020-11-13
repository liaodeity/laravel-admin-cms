<?php

namespace App\Presenters;

use App\Transformers\ConfigTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ConfigPresenter.
 * @package namespace App\Presenters;
 */
class ConfigPresenter extends FractalPresenter
{
    /**
     * Transformer
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ConfigTransformer();
    }
    
    public function getContextFormerToString($type, $context)
    {
        $str = '';
        switch ($type) {
            case 'item':
                $arr = $this->getContextFormer($type, $context);
                $str = implode("，", $arr);
                break;
        }
        
        return $context;
    }
    
    public function getContextFormer($type, $context)
    {
        $ret = null;
        switch ($type) {
            case 'item':
                $ret = @json_decode($context, true);
                
                $ret = $ret ? $ret : [];
                break;
        }
        
        return $ret;
    }
}
