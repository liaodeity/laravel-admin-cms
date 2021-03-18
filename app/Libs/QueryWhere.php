<?php
/**
 * User: gui
 */

namespace App\Libs;

use Illuminate\Support\Facades\DB;

class QueryWhere
{
    protected static $request    = [];
    protected static $orderField = '';
    protected static $order      = 'DESC';

    //request
    public static function setRequest ($request)
    {

        if (array_get ($request, 'field')) {
            self::$orderField = array_get ($request, 'field');
        }
        if (array_get ($request, 'order')) {
            self::$order = array_get ($request, 'order');
        }
        if (array_get ($request, 'searchParams')) {
            $arr = json_decode (array_get ($request, 'searchParams', []), true);
            if (is_array ($arr)) {
                foreach ($arr as $key => $val) {
                    $request[ $key ] = $val;
                }
            }
        }
        self::$request = $request;
    }

    public static function getRequestValue ($field)
    {

        $val = isset(self::$request[ $field ]) ? self::$request[ $field ] : null;
        if (is_null ($val) && strstr ($field, '.')) {
            list($tab, $key) = explode ('.', $field);
            if ($key) {
                $val = isset(self::$request[ $key ]) ? self::$request[ $key ] : null;
            }
        }

        return $val ? trim($val) : $val;
    }

    public static function input ($key, $default = null)
    {
        if (isset(self::$request[ $key ])) {
            return self::$request[ $key ] ?? $default;
        } else {
            return $default;
        }
    }

    //select *
    public static function select (&$M, $val)
    {
        $M = $M->select ($val);
    }

    //where =?
    public static function eq (&$M, $field, $val = null)
    {
        if (is_null ($val)) {
            $val = self::getRequestValue ($field);
        }
        if ($val != '')
            $M = $M->where ($field, $val);
    }

    //where in(?)
    public static function in (&$M, $field, $val = null)
    {
        if (is_null ($val)) {
            $val = self::getRequestValue ($field);
        }
        if (is_string ($val) && $val) {
            $val = explode (',', $val);
        }
        if (!empty($val)) {
            $M = $M->whereIn ($field, $val);
        }
    }

    //wehre not in(?)
    public static function notIn (&$M, $field, $val = [])
    {
        if (!empty($val)) {
            $M = $M->whereNotIn ($field, $val);
        }
    }

    //where like '%?%'
    public static function like (&$M, $field, $val = null)
    {
        if (is_null ($val)) {
            $val = self::getRequestValue ($field);
        }
        if ($val != '')
            $M = $M->where ($field, 'like', "%$val%");
    }

    //region where '%||%'
    public static function region (&$M, $field, $val = null)
    {
        if (is_null ($val)) {
            $val = self::getRequestValue ($field);
        }
        if ($val != '')
            $M = $M->where ($field, 'like', "%|$val|%");
    }

    // where date>=? and date<=?
    public static function date (&$M, $field, $s_val = null, $e_val = null)
    {
        if (is_null ($s_val)) {
            $s_val = self::getRequestValue ($field . '_start');
        }
        if (is_null ($e_val)) {
            $e_val = self::getRequestValue ($field . '_end');
        }
        if (is_null ($s_val) && is_null ($e_val)) {
            $date_str = self::getRequestValue ($field);
            $date     = array_get_date ($date_str);
            $s_val = $date['_start'] ?? null;
            $e_val = $date['_end'] ?? null;
        }
        if ($s_val) $M = $M->where ($field, '>=', $s_val . ' 00:00:00');
        if ($e_val) $M = $M->where ($field, '<=', $e_val . ' 23:59:59');
    }

    //where time>=? and time<=?
    public static function time (&$M, $field, $s_val = null, $e_val = null)
    {

        if (is_null ($s_val)) {
            $s_val = self::getRequestValue ($field . '_start');
        }

        if (is_null ($e_val)) {
            $e_val = self::getRequestValue ($field . '_end');
        }

        if (is_null ($s_val) && is_null ($e_val)) {
            $date_str = self::getRequestValue ($field);
            $date     = array_get_date ($date_str);
            $s_val = $date['_start'] ?? null;
            $e_val = $date['_end'] ?? null;
        }

        if ($s_val) $M = $M->where ($field, '>=', $s_val);
        if ($e_val) $M = $M->where ($field, '<=', $e_val);
    }

    //order by ?
    public static function orderBy (&$M, $field = null, $order = null)
    {
        if (!is_null ($field)) {
            self::$orderField = $field;
        }
        if (!is_null ($order)) {
            self::$order = $order;
        }
        if (self::$orderField && self::$order) {
            $M = $M->orderBy (self::$orderField, self::$order);
        }
    }

    //设置默认的排序方式
    public static function defaultOrderBy ($field = null, $order = null)
    {
        if (empty (self::$orderField) && $field) {
            self::$orderField = $field;
        }
        if ($order) {
            self::$order = $order;
        }

        return new self;
    }

    //企业信息限制
    public static function company (&$M, $table = '')
    {

    }
}
