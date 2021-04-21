<?php
/*
|-----------------------------------------------------------------------------------------------------------
| laravel-admin-cms [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( MIT )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2020-2021 https://gitee.com/liaodeiy/laravel-admin-cms All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/

namespace App\Enums;


abstract class BaseEnum
{
    /*中文*/
    protected static $ATTRS = [];
    /*名称*/
    protected static $VALUES = [];

    public static function toName ($value)
    {
        $values = self::values ();

        return isset($values[ $value ]) ? $values[ $value ] : null;
    }

    public static function values ()
    {
        if (empty(self::$VALUES)) {
            $attrs = self::attrs ();

            $values = array_values ($attrs);
            $data   = [];
            foreach ($values as $value) {
                $data[ $value ] = $value;
            }

            return $data;

        } else {
            return self::$VALUES;
        }
    }

    public static function attrs ()
    {
        return self::$ATTRS;
    }

    public static function toLabel ($value)
    {
        return isset(self::$ATTRS[ $value ]) ? self::$ATTRS[ $value ] : null;
    }

    public static function exists ($value)
    {
        foreach (self::values () as $item) {
            if ($value === $item) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function getKV ()
    {
        $data = [];
        foreach (self::attrs () as $key => $value) {
            $data[] = [
                'key'   => $key,
                'value' => $value,
            ];
        }

        return $data;
    }
}
