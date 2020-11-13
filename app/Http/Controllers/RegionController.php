<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2019/12/25
 */

namespace App\Http\Controllers;


use App\Entities\AgentRegion;
use App\Entities\Region;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegionController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    public function __construct (Request $request)
    {

        $this->request = $request;
    }

    /**
     * 区域列表 add by gui
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function select_area ()
    {
        $source       = $this->request->source ?? '';//特殊来源
        $source_value = $this->request->input ($source, 0);//
        $callback     = $this->request->callback ?? 'default';
        $provinces    = $this->getPidList ([0], []);
        $cities       = [];
        $counties     = [];
        $towns        = [];
        $communities  = [];
        $maxLevel     = $this->request->level ?? 5;
        $more         = $this->request->more ?? 1;
        $ids          = $this->request->ids ?? '';
        $province_id  = [];
        $city_id      = [];
        $county_id    = [];
        $town_id      = [];
        $community_id = [];
        if ($ids) {
            $idArr        = explode (',', $ids);
            $_list        = Region::whereIn ('id', $idArr)->get ();
            $currentLevel = 0;
            foreach ($_list as $item) {
                if ($item->level > $maxLevel) {
                    $maxLevel = $item->level;
                }
                if ($item->level > $currentLevel) {
                    $currentLevel = $item->level;
                }
                if ($item->province_id)
                    $province_id[] = $item->province_id;
                if ($item->city_id)
                    $city_id[] = $item->city_id;
                if ($item->county_id)
                    $county_id[] = $item->county_id;
                if ($item->town_id)
                    $town_id[] = $item->town_id;
                if ($item->community_id)
                    $community_id[] = $item->community_id;
            }

            if (!empty($province_id)) {
                $provinces = $this->getPidList ([0], $province_id);
                $cities    = $this->getPidList ($province_id, $city_id);
            }
            if (!empty($city_id)) {
                $counties = $this->getPidList ($city_id, $county_id);
            }
            if (!empty($county_id)) {
                $towns = $this->getPidList ($county_id, $town_id);
            }
            if (array_sum ($county_id) == 0 && $currentLevel > 3) {
                //无县区
                $counties = [];
                $towns    = $this->getPidList ($city_id, $town_id);
            }
            if (!empty($town_id)) {
                $communities = $this->getPidList ($town_id, $community_id);
            }
        }
        if ($source == 'agent') {
            //代理商；代理区域，排除其他代理商已选择的区域
            //            AgentRegion::whereNotIn('agent_id', $source_value)->get();
        }

        return view ('region.select_area', compact ('callback', 'provinces', 'cities', 'counties', 'towns', 'communities', 'maxLevel', 'more', 'source', 'source_value'));
    }

    /**
     * 获取下一级列表 add by gui
     * @param       $pids
     * @param array $select
     * @return array
     */
    protected function getPidList ($pids, $select = [])
    {
        if (empty($pids))
            return [];
        $source       = $this->request->source ?? '';//特殊来源
        $source_value = $this->request->input ($source, 0);//
        $pids         = array_unique ($pids);
        $regions      = Region::whereIn ('pid', $pids)->where ('status', 1);

        if ($source == 'agent') {
            //排除代理商已代理区域
            $regions = $regions->whereRaw (DB::raw (" id NOT IN(SELECT
	ar.proxy_region_id
FROM
	`tb_agent_regions` ar
INNER JOIN tb_agents a ON a.id = ar.agent_id
WHERE
	a.`status` = 1
AND ar.agent_id <> $source_value)"));
        }
        $regions = $regions->get ();
        foreach ($regions as $region) {
            if (in_array ($region->id, $select)) {
                $region->active = 'active';
            }
        }

        return $regions;
    }

    /**
     * 获取下一级区域列表 add by gui
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegionPid ()
    {
        $source       = $this->request->source ?? '';//特殊来源
        $source_value = $this->request->input ($source, 0);//
        //$level     = $this->request->level ?? 0;
        $pid       = $this->request->pid ?? 0;
        $select    = $this->request->select ?? '';
        $selectArr = explode (',', $select);
        //if($level == 1){
        //    $pid = 0;
        //}
        $regions = Region::where ('pid', $pid);

        if ($source == 'agent') {
            //排除代理商已代理区域
            $regions = $regions->whereRaw (DB::raw (" id NOT IN(SELECT
	ar.proxy_region_id
FROM
	`tb_agent_regions` ar
INNER JOIN tb_agents a ON a.id = ar.agent_id
WHERE
	a.`status` = 1
AND ar.agent_id <> $source_value)"));
        }

        $regions = $regions->get ();
        $list    = [];
        foreach ($regions as $region) {
            $list[] = [
                'id'      => $region->id,
                'pid'     => $region->pid,
                'level'   => $region->level,
                'name'    => $region->name,
                'area'    => $region->area_region_name,
                '_active' => in_array ($region->id, $selectArr) ? 'active' : ''
            ];
        }
        $response = [
            'message' => trans ('修改成功'),
            'result'  => [
                'list' => $list
            ],
        ];

        return response ()->json ($response);
    }

    /**
     * 根据区域名称获取ID add by gui
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegionStrId ()
    {
        $region_str = $this->request->region_str ?? '';
        $region     = Region::where ('area_region_name', $region_str)->first ();
        $region_id  = 0;
        if (isset($region->id)) {
            $region_id = $region->id;
        }
        if (empty($region_id)) {
            $regionArr  = explode ('-', $region_str);
            $regionArr  = array_unique ($regionArr);
            $region_str = implode ('-', $regionArr);
            $region     = Region::where ('area_region_name', $region_str)->first ();
            if (isset($region->id)) {
                $region_id = $region->id;
            }
            //
            if (empty($region_id)) {
                unset($regionArr[ count ($regionArr) - 1 ]);
                $region_str = implode ('-', $regionArr);
                $region     = Region::where ('area_region_name', $region_str)->first ();
                if (isset($region->id)) {
                    $region_id = $region->id;
                    $info      = Region::where ('pid', $region_id)->where ('status', 1)->first ();
                    if (isset($info->id)) {
                        $region_id = $info->id;
                    }

                }
            }

        }
        if (empty($region_id)) {
            $arr = [
                'error'     => true,
                'message'   => '无获取id',
                'region_id' => $region_id
            ];
        } else {
            $arr = [
                'message'   => '获取id',
                'region_id' => $region_id
            ];
        }

//        Log::info ('区域获取' . $region_str, $arr);

        return response ()->json ($arr);
    }

    /**
     * 根据名称获取上一级信息 add by gui
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegionStrPid ()
    {

        $region_str = $this->request->region_str ?? '';
        $name       = $this->request->name ?? '';
        $level      = $this->request->level ?? '';

        $regionArr = explode ('-', $region_str);
        $arr       = [];
        $arr[]     = $name;
        foreach ($regionArr as $key => $item) {
            //if (($key + 1) >= $level) {
            //    continue;
            //}
            if ($item) {
                $arr[] = $item;
            }
        }

        $arr          = array_unique ($arr);
        $province     = [];
        $city         = [];
        $county       = [];
        $town         = [];
        $province_pid = 0;
        $city_id      = 0;
        $county_id    = 0;

        //foreach ($arr as $key => $item) {
        //    if ($key === 0) {
        //        $pid          = 0;
        //        $region       = Region::where('pid', $pid)->where('name', $item)->first();
        //        $province_pid = $region->id ?? 0;
        //    } elseif ($key == 1) {
        //        $region  = Region::where('pid', $province_pid)->where('name', $item)->first();
        //        $city_id = $region->id ?? 0;
        //        //                dd($province_pid);
        //        //                dd($region);
        //    } elseif ($key == 2) {
        //        $region    = Region::where('pid', $city_id)->where('name', $item)->first();
        //        $county_id = $region->id ?? 0;
        //    }
        //}
        if (empty($province_pid)) {
            $info         = Region::where ('level', $level)->where ('name', $name)->first ();
            $province_pid = $info->province_id ?? 0;
            $city_id      = $info->city_id ?? 0;
            $county_id    = $info->county_id ?? 0;
        }
        if (empty($province_pid)) {
            $regions = Region::where ('pid', 0)->get ();
            foreach ($regions as $key => $region) {
                if (empty($province_pid) && $key == 0) {
                    $province_pid = $region->id;
                }
                $province[] = $region->name;
            }
        }
        //        var_dump($city_id);
        $regions = Region::where ('pid', $province_pid)->get ();
        foreach ($regions as $key => $region) {
            if (empty($city_id) && $key == 0) {
                $city_id = $region->id;
            }
            //已选城市
            if ($level == 2 && $region->name == $name) {
                $info    = Region::where ('pid', $region->pid)->where ('name', $name)->first ();
                $city_id = $info->id ?? $city_id;
            }
            $city[] = $region->name;
        }

        $regions = Region::where ('pid', $city_id)->get ();
        foreach ($regions as $key => $region) {
            if (empty($county_id) && $key == 0) {
                $county_id = $region->id;
            }
            //已选县区
            if ($level == 3 && $region->name == $name) {
                $info      = Region::where ('pid', $region->pid)->where ('name', $name)->first ();
                $county_id = $info->id ?? $county_id;
            }
            $county[] = $region->name;
        }
        $debug['$province_pid'] = $province_pid;
        $debug['$name']         = $name;
        $debug['$arr']          = $arr;
        $debug['$level']        = $level;
        $debug['$region_str']   = $region_str;
        $regions                = Region::where ('pid', $county_id)->get ();
        foreach ($regions as $key => $region) {
            $town[] = $region->name;
        }
        $arr = [
            'message'  => '获取id',
            'debug'    => $debug,
            'province' => $province,
            'city'     => $city,
            'county'   => $county,
            'town'     => $town,
        ];
//        Log::info ('区域列表' . $region_str, $arr);

        return response ()->json ($arr);
    }

    /**
     * @deprecated
     * 已作废
     */
    public function setJs ()
    {
        set_time_limit (0);
        $list         = Region::select ('name', 'id', 'pid')->where ('pid', 0)->get ();
        $jsVar        = [];
        $provincesArr = [];
        $provinces    = [];
        foreach ($list as $province) {
            //身份
            $provincesArr[] = $province->name;
            $list2          = Region::select ('name', 'id', 'pid')->where ('pid', $province->id)->get ();
            $citiesArr      = [];
            $cities         = [];
            foreach ($list2 as $city) {
                //城市
                $citiesArr[] = $city->name;
                //$arr[]       = $city->name;
                $list3       = Region::select ('name', 'id', 'pid')->where ('pid', $city->id)->get ();
                $countiesArr = [];
                $counties    = [];
                foreach ($list3 as $county) {
                    //县区
                    $countiesArr[] = $county->name;

                    $list4    = Region::select ('name', 'id', 'pid')->where ('pid', $county->id)->get ();
                    $townsArr = [];
                    $towns    = [];
                    foreach ($list4 as $town) {
                        $townsArr[]           = $town->name;
                        $towns[ $town->name ] = $town->id;
                    }
                    $counties[ $county->name ]["id"]       = $county->id;
                    $counties[ $county->name ]["townsArr"] = empty($townsArr) ? [""] : $townsArr;
                    $counties[ $county->name ]["towns"]    = empty($towns) ? [""] : $towns;
                }
                $cities[ $city->name ]["id"]       = $city->id;
                $cities[ $city->name ]["areasArr"] = $countiesArr;
                $cities[ $city->name ]["areas"]    = $counties;
            }
            $provinces[ $province->name ]["id"]        = $province->id;
            $provinces[ $province->name ]["citiesArr"] = $citiesArr;
            $provinces[ $province->name ]["cities"]    = $cities;
        }
        $result["provincesArr"] = $provincesArr;
        $result["provinces"]    = $provinces;


        $content = json_encode ($result, JSON_UNESCAPED_UNICODE);
        $content = 'var regions = ' . $content;
        file_put_contents ('js/regionsObject.js', $content);

    }
}
