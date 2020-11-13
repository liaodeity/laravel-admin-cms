<?php

namespace App\Repositories;

use App\Entities\Agent;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\RegionRepository;
use App\Entities\Region;
use App\Validators\RegionValidator;

/**
 * Class RegionRepositoryEloquent.
 * @package namespace App\Repositories;
 */
class RegionRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model()
    {
        return Region::class;
    }

    /**
     * Specify Validator class name
     * @return mixed
     */
    public function validator()
    {

        return RegionValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 同步区域解析字段
     * add by gui
     */
    public function syncAreaRegion()
    {
        $list = Region::where('province_id' , 0)->limit(20)->get();
        foreach ($list as $item) {
            $region               = Region::find($item->id);
            $region->province_id  = $region->getLevelID($item->id, 1);
            $region->city_id      = $region->getLevelID($item->id, 2);
            $region->county_id    = $region->getLevelID($item->id, 3);
            $region->town_id      = $region->getLevelID($item->id, 4);
            $region->community_id = $region->getLevelID($item->id, 5);
            $region->save();
        }
        $list = Region::where(['area_region' => ''])->where('province_id', '>', 0)->get();

        foreach ($list as $item) {
            $region                   = Region::find($item->id);
            $area_region              = $region->getRegionArea($item->id);
            $area_region_name         = $region->getLevelName($item->id);
            $region->area_region      = $area_region;
            $region->area_region_name = $area_region_name;
            $ret                      = $region->save();
            if (!$ret) {
                throw new \ErrorException('更新区域数据失败');
            }

        }
    }

    public function allowDelete($id)
    {
        return false;//
    }

}
