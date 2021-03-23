<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2021/3/23
 */

namespace App\Libs;


use Illuminate\Support\Str;

/**
 *
 * Class Parameter
 * @method static genderItem($ind = 'all', $html = false)
 * @method static userStatusItem($ind = 'all', $html = false)
 * @package App\Libs
 */
class Parameter
{
    public static function __callStatic ($method, $parameters)
    {
        if (substr ($method, -4) == 'Item') {
            $field = str_replace ('Item', '', $method);
            $field = Str::snake ($field);
            return (new static)->item ($field, ...$parameters);
        }
    }

    protected function item ($field, $ind = 'all', $html = false)
    {
        return get_item_parameter ($field, $ind, $html);
    }

}
