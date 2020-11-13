<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/30
 */

namespace App\Http\Controllers;


use App\Entities\Order;
use App\Exports\CommonExport;
use App\Libs\ExportMame;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportBaseController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    protected $reportSql = '';
    /**
     * @var string
     */
    protected $functionName = '';
    /**
     * @var string
     */
    protected $excelName    = '';
    protected $reportParams = [];
    protected $moduleName   = '';
    /**
     * @var string
     */
    protected $orderBy = ' ';
    protected $limit   = 10;

    /**
     * 获取统计数 add by gui
     */
    protected function getReportCount ()
    {
        $params   = $this->reportParams;
        $sql      = $this->reportSql;
        $page_num = empty($this->request->page) ? 1 : $this->request->page;
        $page_row = $this->limit;
        $count    = DB::select ($sql, $params);
        $sql      .= $this->orderBy ? (" ORDER BY " . $this->orderBy) : '';
        //dd ($sql);
        if (!isset($this->request->export)) {

            $sql                   .= " LIMIT :start_limit,:end_limit";
            $params['start_limit'] = ($page_num - 1) * $page_row;
            $params['end_limit']   = $page_row;
        }
        $total = count ($count);
        //dd ($params);
        $pagination = new LengthAwarePaginator('', $total, $page_row);
        $page       = html_entity_decode ($pagination->render ());
        //        echo $sql;
        $data = DB::select ($sql, $params);
        //        dd($data);
        $Export = new CommonExport($data, 'report_' . $this->moduleName . '_' . $this->functionName);
        if (isset($this->request->export)) {
            return Excel::download ($Export, $this->excelName . '.xlsx');
        }
        if ($this->request->wantsJson ()) {
            $html = $Export->view ()->render ();

            return response ()->json ([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        } else {
            return view ($this->moduleName . '.reports.' . $this->functionName);
        }
    }

    /**
     * 获取报表时间统计类别 add by gui
     * @param      $field
     * @param null $countType
     * @return string
     */
    protected function getCountTypeDate ($field, $countType = null)
    {
        if (is_null ($countType)) {
            $countType = $this->request->countType ?? 'day';
        }
        switch ($countType) {
            case 'day':
                $field_count = "date($field)";
                break;
            case 'month':
                $field_count = "DATE_FORMAT($field,'%Y年%m月')";
                break;
            case 'season':
                $field_count = "CONCAT(
		YEAR ($field),
		'年',
		FLOOR(
			(date_format($field, '%m') + 2) / 3
		),
		'季'
	)";
                break;
            case 'year':
                $field_count = "CONCAT(YEAR ($field),'年')";
                break;
        }

        return $field_count;
    }

    /**
     * 设置导出名称 add by gui
     * @param null $all
     * @param      $name
     * @param      $params
     */
    protected function setExportName($all = null, $name, $params)
    {
        if(is_null($all)){
            $all = $this->request->all();
        }
        if(isset($all['countType']) && $all['countType']){
            $countType = [
                'day' => '每日',
                'month' => '每月',
                'season' => '每季',
                'year' => '每年',
                'province_id'=>'省',
                'city_id'=>'市',
                'county_id'=>'县',
                'town_id'=>'区'
            ];
            $all['countType'] = $countType[$all['countType']] ?? $all['countType'];
        }
        if(isset($all['level']) && $all['level']){
            $all['level'] = '前'.$all['level'].'名';
        }
//        dd($params);
        $ExportName = new ExportMame();
        $export_name = $ExportName->setRequest($all)->getName($name, $params);
        $this->excelName = $export_name;
    }
}
