<?php

namespace App\Entities;

use App\Services\MapService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Region.
 * @package namespace App\Entities;
 */
class Region extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable
        = [
            'pid',
            'name',
            'level',
            'area_region',
            'area_region_name',
            'province_id',
            'city_id',
            'county_id',
            'town_id',
            'community_id',
            'lat',
            'lnt',
            'status',
        ];

    public function levelItem($ind = 'all', $html = false)
    {
        return get_item_parameter('area_level', $ind, $html);
    }


    public function statusItem($ind = 'all', $html = false)
    {
        return get_item_parameter('show_status', $ind, $html);
    }

    public function getFindName($id)
    {
        if (empty($id)) return '';
        $info = $this->where('id', $id)->first();

        //dd($info);
        return $info ? $info->name : '';
    }

    /**
     * 获取区域等级名称 add by gui
     * @param $id
     * @return mixed|string
     */
    public function getLevelName($id)
    {
        if (empty($id)) return '';
        if (config('app.debug') === false)
            $regionArr = Cache::get('region_area_name');
        if (empty($regionArr)) $regionArr = [];
        $info = $this->where('id', $id)->first();
        if (isset($regionArr[$id])) return $regionArr[$id];
        if ($info) {
            $arr            = [];
            $arr[]          = $this->getFindName($info->province_id);
            $arr[]          = $this->getFindName($info->city_id);
            $arr[]          = $this->getFindName($info->county_id);
            $arr[]          = $this->getFindName($info->town_id);
            $arr[]          = $this->getFindName($info->community_id);
            $arr            = array_filter($arr);
            $regionArr[$id] = implode('-', $arr);
            Cache::forever('region_area_name', $regionArr);
        }

        return isset($regionArr[$id]) ? $regionArr[$id] : '';
    }

    /**
     * 获取区域的ID拼接 add by gui
     * @param $id
     * @return string
     */
    public function getRegionArea($id)
    {
        if (empty($id)) return '';
        $info = $this->where('id', $id)->first();
        if ($info) {
            $arr   = [];
            $arr[] = $info->province_id;
            $arr[] = $info->city_id;
            $arr[] = $info->county_id;
            $arr[] = $info->town_id;
            $arr[] = $info->community_id;
            $arr   = array_filter($arr);
            $str   = implode('|', $arr);
            $str   = trim($str, '|');
            $str   = '|' . $str . '|';
        }

        return isset($str) ? $str : '';
    }

    /**
     * 根据等级获取对应等级区域ID add by gui
     * @param $id
     * @param $level
     * @return int
     */
    public function getLevelID($id, $level)
    {
        $info = $this->find($id);
        if ($info && $info->level == $level) {
            return $info->id;
        }
        if ($info->pid) {
            return $this->getLevelID($info->pid, $level);
        }
        return 0;
    }

    /**
     * 获取更多区域列表 add by gui
     * @param $regionIds
     * @return array
     */
    public function getMoreRegionList($regionIds)
    {
        $regions   = $this->whereIn('id', $regionIds)->get();
        $regionArr = [];
        foreach ($regions as $region) {
            $pid                       = $region->pid;
            $regionArr[$pid]['id'][]   = $region->id;
            $regionArr[$pid]['name'][] = $region->name;
        }
        $result = [];
        foreach ($regionArr as $pid => $region) {
            $result[] = [
                'region_id_str'   => implode(',', $region['id']),
                'region_name_str' => implode('、', $region['name']),
                'region_pid_name' => $this->getLevelName($pid),
            ];
        }
        return $result;
    }

    /**
     * 获取中心点坐标
     * add by gui
     * @param $regionID
     * @return array
     * @throws \ErrorException
     */
    public function getCenterCoordinate($regionID)
    {
        $region = $this->find($regionID);
        $lat    = $region->lat;
        $lng    = $region->lng;
        if ($lat && $lng) {
            return ['lat' => $lat, 'lng' => $lng];
        }
        //
        $name       = $this->getLevelName($regionID);
        $name       = str_replace('-', '', $name);
        $MapService = new MapService();
        $ret        = $MapService->addressToCoordinate($name);
        if (isset($ret['lat']) && $ret['lat'] && $ret['lng']) {
            $region->lat = $ret['lat'];
            $region->lng = $ret['lng'];
            $region->save();
            return $ret;
        }
        throw new \ErrorException('无法获取到区域坐标');
    }

    /**
     * 获取区域名称数组 add by gui
     * @param $id
     * @return false|string
     */
    public static function getRegionNameStrArr($id)
    {
        $region = Region::find($id);

        $province_id  = $region->province_id ?? 0;
        $city_id      = $region->city_id ?? 0;
        $county_id    = $region->county_id ?? 0;
        $town_id      = $region->town_id ?? 0;
        $community_id = $region->community_id ?? 0;
        $list         = Region::where('pid', 0)->where('status', 1)->get();
        $province     = [];
        foreach ($list as $key => $item) {
            if (empty($province_id)) {
                $province_id = $item->id;
            }
            $province[] = $item->name;
        }
        $city = [];
        if ($province_id) {
            $list = Region::where('pid', $province_id)->where('status', 1)->get();
            foreach ($list as $key => $item) {
                if (empty($city_id)) {
                    $city_id = $item->id;
                }
                $city[] = $item->name;
            }
        }
        $county = [];
        if ($city_id) {
            $list = Region::where('pid', $city_id)->where('status', 1)->get();
            foreach ($list as $key => $item) {
                if (empty($county_id)) {
                    $county_id = $item->id;
                }
                $county[] = $item->name;
            }
        }
        $town = [];
        if ($county_id) {
            $list = Region::where('pid', $county_id)->where('status', 1)->get();
            foreach ($list as $key => $item) {
                if (empty($town_id)) {
                    $community_id = $item->id;
                }
                $town[] = $item->name;
            }
        }
        $arr= [
            'province'=>$province,
            'city'=>$city,
            'county'=>$county,
            'town'=>$town
        ];
        return json_encode($arr,JSON_UNESCAPED_UNICODE);
    }
}
