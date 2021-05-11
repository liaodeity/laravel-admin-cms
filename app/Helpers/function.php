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

require_once "my_function.php";

use Illuminate\Support\Carbon;

##########################################
#系统助手函数库，自定义函数请my_function.php#
#########################################

function array_to_object ($arr)
{
    $json = json_encode ($arr);

    return json_decode ($json);
}

/**
 * 把返回的数据集转换成Tree
 * @param array  $list 要转换的数据集
 * @param string $pk
 * @param string $pid  parent标记字段
 * @param string $child
 * @param int    $root
 * @return \Illuminate\Support\Collection
 */
function list_to_tree ($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array ($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[ $data[ $pk ] ] =& $list[ $key ];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[ $pid ];
            if ($root == $parentId) {
                $tree[] =& $list[ $key ];
            } else {
                if (isset($refer[ $parentId ])) {
                    $parent             =& $refer[ $parentId ];
                    $parent[ $child ][] =& $list[ $key ];
                }
            }
        }
    }
    $tree = array_to_object ($tree);
    $tree = collect ($tree);

    return $tree;
}

/**
 * 获取列标识
 * 目前最大支持702列
 */
function get_key_num ()
{

    $arr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $num = $arr;

    foreach ($arr as $key1 => $value1) {
        foreach ($arr as $key2 => $value2) {
            $num[] = $value1 . $value2;
        }
    }

    return $num;
}

/**
 * 验证手机号是否正确
 */
function is_mobile ($mobile)
{
    if (!is_numeric ($mobile)) {
        return false;
    }

    return preg_match ('#^([0])?([1])([0-9]{10,12})$#', $mobile) ? true : false;
}


/**
 * 验证固定电话是否正确
 */
function is_phone ($Phone)
{
    if (!is_numeric ($Phone)) {
        return false;
    }

    $len = strlen ($Phone);

    switch ($len) {
        case 4: // 内线电话
            return preg_match ('#^[1-9][0-9]{3}$#', $Phone) ? true : false;
            break;
        case 5: // 5 位长特殊电话
            return preg_match ('#^[1]\d{4}$#', $Phone) ? true : false;
            break;
        case 6: // 移动 集群网
            return preg_match ('#^[56]\d{5}$#', $Phone) ? true : false;
            break;
    }

    if (strpos ($Phone, '00853') === 0) {
        // 澳门电话
        // 非 0 开头 7-8 位数
        return preg_match ('#^(00853)[1-9]\d{6,7}$#', $Phone) ? true : false;
    }
    if (strpos ($Phone, '00852') === 0) {
        // 香港电话
        // 非 0 开头 8 位数
        return preg_match ('#^(00852)[1-9]\d{7}$#', $Phone) ? true : false;
    }
    if (strpos ($Phone, '00886') === 0) {
        // 台湾电话
        // 非 0 开头 8~9 位长
        return preg_match ('#^(00886)[1-9]\d{7,9}$#', $Phone) ? true : false;
    }

    # 中国固话
    # 1 国家代码 空，或者是以下几种
    #   空，
    #   0086  中国
    #   00852 香港
    #   00853 澳门
    #   00886 台湾
    # 2 区号，空，或者 0 开头 3 至 4 位长，如：
    #   空，
    # 3 实际号码，非 0 开头，7 至 8 位长，
    #   非空
    #  ((0086)(0[1-9]\d{0,2})) - 处理以 0086 + 区号 的形式出现的 不能没有区号！
    #  (0[1-9]\d{0,2})?  以 0xx 0xxx 形式出现的区号，可以有或者没有
    #  [1-9]\d{6,7}      第一位不是 0 ，后 6 或者 7 位为数字 的电话号码。
    #
    # ( ((0086)(0[1-9]\d{0,2})) | (0[1-9]\d{0,2})?   )[1-9]\d{6,7}
    #
    #

    return preg_match ('#^(((0086)(0[1-9]\d{0,2}))|(0[1-9]\d{0,2})?)[0-9]\d{6,8}$#', $Phone) ? true : false;
}

/**
 * 手机或固话
 * @param $Phone
 * @return bool
 */
function is_phone_or_mobile ($Phone)
{
    if (!is_numeric ($Phone)) {
        return false;
    }

    $ismobile = is_mobile ($Phone);
    $isphone  = is_phone ($Phone);

    return $ismobile or $isphone;
}

/**
 * 判断是否有权限 add by gui
 * @param $auth
 * @return bool
 */
function check_admin_auth ($auth)
{
    $auth = trim ($auth);
    $uid  = get_login_user_id ();
    if (empty($uid)) {
        return false;
    }
    if (!$auth) {
        return true;
    }
    $auth = str_replace (' ', '_', $auth);
    if ($auth) {
        $permission = \Spatie\Permission\Models\Permission::findOrCreate ($auth);
        if (!$permission->menu_id) {
            $arr = explode ('_', $auth);
            if (count ($arr) >= 2) {
                $opt      = array_last ($arr);
                $lang_key = 'auth.operate.' . $opt;
                $title    = __ ($lang_key);
                if ($title == $lang_key) {
                    $title = $opt;
                }
                unset($arr[ count ($arr) - 1 ]);
                $auth_name           = implode ('_', $arr);
                $menuId              = \App\Models\Menu::where ('auth_name', $auth_name)->value ('id');
                $permission->menu_id = (int)$menuId;
                $permission->title   = $title;
                $permission->save ();
            }
        }
    }

    $user    = \App\Models\User::find ($uid);
    $isCheck = $user->hasPermissionTo ($auth);
    //超级管理员
    $isSuper = $user->hasRole ('super');
    if ($isSuper) {
        $isCheck = true;
    }

    return $isCheck ? true : false;
}

/**
 *  add by gui
 * @param null $msg
 * @return \Illuminate\Http\JsonResponse|void
 */
function auth_error_return ($msg = null)
{
    if (is_null ($msg)) {
        $msg = __ ('auth.no_auth_error');
    }
    if (request ()->wantsJson ()) {
        return ajax_error_result ($msg);
    } else {
        return abort (403, $msg);
    }
}

/**
 * 格式化文件大小单位 add by gui
 * @param $size
 * @return string
 */
function format_size ($size)
{
    $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    if ($size == 0) {
        return ('n/a');
    } else {
        return (round ($size / pow (1024, ($i = floor (log ($size, 1024)))), 2) . $sizes[ $i ]);
    }
}

/**
 * 获取config内容 add by gui
 * @param        $key
 * @param string $default
 * @return mixed
 */
function get_config_value ($key, $default = '')
{
    $value = \App\Models\Config::getValue ($key, $default);

    return $value;
}

/**
 * 移除非中文字符 add by gui
 * @param $str
 * @return string
 */
function remove_no_chinese ($str)
{
    preg_match_all ('/[\x{4e00}-\x{9fff}]+/u', $str, $matches);
    $str = join ('', $matches[0]);

    return $str;
}

/**
 * 检查手机号码格式是否正确 add by gui
 * @param $mobile
 * @return bool
 */
function check_mobile ($mobile)
{
    $MobileNumber = new \App\Libs\MobileNumber();
    $check        = $MobileNumber->check ($mobile);

    return $check ? true : false;

}

/**
 * 验证身份证号码格式是否正确 add by gui
 * @param $id_number
 * @return bool
 */
function check_id_number ($id_number)
{
    $IdCard = new  \App\Libs\IdCard();
    $check  = $IdCard->validation_filter_id_card ($id_number);

    return $check ? true : false;
}

/**
 * 范围显示 add by gui
 * @param        $min
 * @param        $max
 * @param string $unit
 * @param string $joiner
 * @return string
 */
function get_range_display ($min, $max, $unit = '', $joiner = ' ~ ')
{
    $arr = [];
    if (!empty($min)) {
        $arr[] = $min . $unit;
    }
    if (!empty($max)) {
        $arr[] = $max . $unit;
    }

    return implode ($joiner, $arr);
}


/**
 * 将文本换行\n进行替换成p标签显示，
 * 视图需使用{!! parse_text_ln_p($text) !!} add by gui
 * @param $text
 * @return string
 */
function parse_text_ln_p ($text)
{
    $arr = explode ("\n", $text);
    $str = '';
    foreach ($arr as $val) {
        $str .= '<p>' . $val . '</p>';
    }

    return $str;
}

/**
 * 根据时间获取年龄 add by gui
 * @param $date
 * @return int|string
 */
function get_age ($date)
{
    if ($date) {
        return Carbon::parse ($date)->age;
    }

    return '';
}

/**
 * 将空字符串转换成null add by gui
 * @param $arr
 * @param $key
 * @return mixed
 */
function empty_value_null (&$arr, $key)
{
    if (isset($arr[ $key ]) && $arr[ $key ] == '') {
        $arr[ $key ] = null;
    }
}

/**
 * 获取时间常数 add by gui
 * @param string $_date [格式：2020-03-11 - 2020-03-20]
 * @param        $key
 * @return array
 */
function array_get_date ($_date, $key = '')
{
    $date = [
        $key . '_start' => null,
        $key . '_end'   => null
    ];
    if ($_date) {
        [$start, $end] = explode (' - ', $_date);
        if ($start) {
            $date[ $key . '_start' ] = $start ? trim ($start) : null;
        }
        if ($start) {
            $date[ $key . '_end' ] = $end ? trim ($end) : null;
        }
    }

    return $date;
}


/**
 * 月份转换成日期，方便保存到数据库 add by lekj
 *
 * @param string $_date [格式：2020-03-11 - 2020-03-20]
 *
 * @return false|string
 */
function month2date ($_date)
{
    if ($_date) {
        $date = date ('Y-m-01', strtotime ($_date));
    } else {
        $date = '';
    }

    return $date;
}

/**
 * 获取数据的数字值 add by gui
 * @param     $array
 * @param     $key
 * @param int $default
 * @return int|mixed
 */
function array_get_number ($array, $key, $default = 0)
{
    $value = array_get ($array, $key, $default);
    if (!is_numeric ($value)) {
        $value = (int)$default;
    }

    return $value;
}


/**
 * 获取键值对配置值 add by gui
 * @param        $key
 * @param string $ind
 * @param bool   $html
 * @return array|\Illuminate\Contracts\Translation\Translator|mixed|string|null
 */
function get_item_parameter ($key, $ind = 'all', $html = false)
{
    $arr = __ ('parameter.' . $key);
    if ($ind !== 'all' && is_array ($arr)) {
        $text      = array_key_exists ($ind, $arr) ? $arr[ $ind ] : null;
        $html_text = $text;
        if ($text) {
            //特殊情况
            $html_text = trans ($text);
        }

        return $html === true ? $html_text : $text;
    }

    return $arr;
}

/**
 * 获取版本号
 * @return string
 */
function get_version ()
{
    $version = config ('app.version');
    $time    = '';
    if (config ('app.debug')) {
        $time = '.' . time ();
    }

    return $version . $time;
}

/**
 * 获取附件路径 add by gui
 * @param $id
 * @return string
 */
function get_file_path ($id)
{
    if (empty($id)) {
        return '';
    }
    $file = \App\Models\Attachment::find ($id);
    $path = $file->path ?? '';

    return $path ? $path : '';
}

/**
 * 显示图片地址 add by gui
 * @param        $id
 * @param string $default 默认图片
 * @return string
 */
function get_picture_src ($id, $default = '')
{
    if (empty($id)) {
        return $default;
    }
    $picture = \App\Models\Picture::find ($id);
    $path    = $picture->path ?? '';

    return $path ? asset ($path) : $default;
}

/**
 * 获取UUID add by gui
 * @return string
 */
function get_uuid ()
{
    $uuid = \Illuminate\Support\Str::uuid ();
    if ($uuid instanceof \Ramsey\Uuid\UuidInterface) {
        return $uuid->toString ();
    } else {
        $str  = md5 (uniqid (mt_rand (), true));
        $uuid = substr ($str, 0, 8) . '-';
        $uuid .= substr ($str, 8, 4) . '-';
        $uuid .= substr ($str, 12, 4) . '-';
        $uuid .= substr ($str, 16, 4) . '-';
        $uuid .= substr ($str, 20, 12);

        return $uuid;
    }
}

/**
 * 当前登录用户 add by gui
 * @return mixed
 */
function get_login_user_id ()
{
    return session ()->get ('LOGIN_USER_ID', 0);
}

/**
 * 返回失败JSON add by gui
 * @param       $msg
 * @param array $result
 * @return \Illuminate\Http\JsonResponse
 */
function ajax_error_result ($msg, $result = [])
{

    $result['code']    = -1;
    $result['message'] = $msg;

    return response ()->json ($result);
}

/**
 * 返回成功JSON add by gui
 * @param       $msg
 * @param array $result
 * @return \Illuminate\Http\JsonResponse
 */

function ajax_success_result ($msg, $result = [])
{
    $result['code']    = 0;
    $result['message'] = $msg;

    return response ()->json ($result);
}
