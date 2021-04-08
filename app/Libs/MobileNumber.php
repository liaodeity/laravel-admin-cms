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

namespace App\Libs;

/**
 * 手机号码格式
 * Class MobileNumber
 * @package App\Libs
 */
class MobileNumber
{
    /**
     * 基本号码格式判断，非强制号码段判断 add by gui
     * @param $mobile
     * @return bool|false|int
     */
    public function check ($mobile)
    {
        $mobile = trim ($mobile);
        $mobile = $this->removePrefixZero ($mobile);
        if (!is_numeric ($mobile)) return false;
        //获取第一个数字
        $first = substr ($mobile, 0, 1);
        //获取后面数字
        $number = substr ($mobile, 1);
        $check  = false;
        switch ($first) {
            case 1:
                //号码开头为1，11位号码段
                $preg = preg_match ('#^1[0-9]{10}$#', $mobile);
                if ($preg) {
                    return true;
                }
                break;
        }

        return $check;

    }

    /**
     * 去除号码前缀+0 add by gui
     * @param $mobile
     * @return bool|string
     */
    protected function removePrefixZero ($mobile)
    {
        //获取第一个数字
        if (substr ($mobile, 0, 1) == '0') {
            return substr ($mobile, 1);
        }

        return $mobile;
    }

    /**
     * 号码格式显示 add by gui
     * @param string $mobile
     * @param string $prefix
     * @return string
     */
    public function format ($mobile, $prefix = '')
    {
        $mobile = $this->removePrefixZero ($mobile);
        $len    = strlen ($mobile);
        $format = '';
        switch ($len) {
            case 11:
                $format = substr ($mobile, 0, 3) . ' '
                    . substr ($mobile, 4, 4) . ' '
                    . substr ($mobile, 8, 4);
                break;
        }

        return $format ? $prefix . $format : $prefix . $mobile;
    }
}
