<?php
/**
 * Created by PhpStorm.
 * User: gui
 */

namespace App\Libs;


class ExportMame
{

    private $request;

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * 获取导出文件名称 add by gui
     * @param       $module
     * @param array $params
     * @return string
     */
    public function getName($module, $params = [])
    {
        $export_name = $module . '';
        $search = '';
        foreach ($params as $key => $param) {
            $value = $this->request[$key] ?? '';
            if ($value) {
                $search .= $param . "_" . $value . ",";
            }
            $start = $this->request[$key . '_start'] ?? '';
            $end = $this->request[$key . '_end'] ?? '';
            if ($start || $end) {
                $search .= $param . "_(" . $start . '至' . $end . ')';
            }
        }
        if ($search) {
            $search = trim($search, ',');
            $export_name .= '[' . $search . ']';
        }
        $export_name .= date('Ymd');
        return $export_name;
    }
}
