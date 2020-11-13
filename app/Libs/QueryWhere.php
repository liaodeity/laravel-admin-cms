<?php
/**
 * User: gui
 */

namespace App\Libs;


use Illuminate\Support\Facades\DB;

class QueryWhere
{
    protected static $request = [];

    //request
    public static function setRequest($request)
    {
        self::$request = $request;
    }

    protected static function getRequestValue($field)
    {

        $val = isset(self::$request[$field]) ? self::$request[$field] : null;
        if (is_null($val) && strstr($field, '.')) {
            list($tab, $key) = explode('.', $field);
            if ($key) {
                $val = isset(self::$request[$key]) ? self::$request[$key] : null;
            }
        }

        return $val;
    }

    //select *
    public static function select(&$M, $val)
    {
        $M = $M->select($val);
    }

    //where =?
    public static function eq(&$M, $field, $val = null)
    {
        if (is_null($val)) {
            $val = self::getRequestValue($field);
        }
        if ($val != '')
            $M = $M->where($field, $val);
    }

    //where in(?)
    public static function in(&$M, $field, $val = [])
    {
        if (!empty($val)) {
            $M = $M->whereIn($field, $val);
        }
    }
    //wehre not in(?)
    public static function notIn(&$M, $field, $val = [])
    {
        if (!empty($val)) {
            $M = $M->whereNotIn($field, $val);
        }
    }

    //where like '%?%'
    public static function like(&$M, $field, $val = null)
    {
        if (is_null($val)) {
            $val = self::getRequestValue($field);
        }
        if ($val != '')
            $M = $M->where($field, 'like', "%$val%");
    }

    //region where '%||%'
    public static function region (&$M, $field, $val = null)
    {
        if (is_null($val)) {
            $val = self::getRequestValue($field);
        }
        if ($val != '')
            $M = $M->where($field, 'like', "%|$val|%");
    }

    // where date>=? and date<=?
    public static function date(&$M, $field, $s_val = null, $e_val = null)
    {
        if (is_null($s_val)) {
            $s_val = self::getRequestValue($field . '_start');
        }
        if (is_null($e_val)) {
            $e_val = self::getRequestValue($field . '_end');
        }
        if ($s_val) $M = $M->where($field, '>=', $s_val . ' 00:00:00');
        if ($e_val) $M = $M->where($field, '<=', $e_val . ' 23:59:59');
    }

    //where time>=? and time<=?
    public static function time(&$M, $field, $s_val, $e_val)
    {
        if (is_null($s_val)) {
            $s_val = self::getRequestValue($field . '_start');
        }

        if (is_null($e_val)) {
            $e_val = self::getRequestValue($field . '_end');
        }

        if ($s_val) $M = $M->where($field, '>=', $s_val);
        if ($e_val) $M = $M->where($field, '<=', $e_val);
    }

    //order by ?
    public static function orderBy(&$M, $orderBy = null)
    {
        if ($orderBy) {
            list($order, $by) = explode(' ', $orderBy);
            $M = $M->orderBy($order, $by);
        }

    }
}
