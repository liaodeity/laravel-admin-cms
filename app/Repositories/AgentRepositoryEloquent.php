<?php

namespace App\Repositories;

use App\Entities\AgentRegion;
use App\Entities\Bill;
use App\Entities\Log;
use App\Entities\Order;
use App\Entities\Region;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AgentRepository;
use App\Entities\Agent;
use App\Validators\AgentValidator;

/**
 * Class AgentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AgentRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Agent::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return AgentValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 保存代理商账号信息 add by gui
     * @param $agentID
     * @param $input
     * @return bool
     * @throws \ErrorException
     */
    public function savePersonal($agentID, $input)
    {
        // 更新代理商资料信息
        $inputAgent       = $input['Agent'] ?? [];
        $inputPassword    = $input['Password'] ?? [];
        $agent            = Agent::find($agentID);
        $company_name     = $inputAgent['company_name'] ?? '';
        $contact_name     = $inputAgent['contact_name'] ?? '';
        $contact_phone    = $inputAgent['contact_phone'] ?? '';
        $office_region_id = $inputAgent['office_region_id'] ?? '';
        $office_address   = $inputAgent['office_address'] ?? '';
        if (!empty($inputAgent)) {
            if (empty($company_name)) {
                throw new \ErrorException('请输入公司名称');
            }
            if (empty($contact_name)) {
                throw new \ErrorException('请输入联系人名称');
            }
            if (empty($contact_phone)) {
                throw new \ErrorException('请输入联系电话');
            }
            if (empty($office_region_id)) {
//                throw new \ErrorException('请输入办公区域');
            }
            if (empty($office_address)) {
                throw new \ErrorException('请输入办公地址');
            }
            $agent->company_name     = $company_name;
            $agent->contact_name     = $contact_name;
            $agent->contact_phone    = $contact_phone;
            $agent->office_region_id = $office_region_id;
            $agent->office_address   = $office_address;
            Log::createLog(Log::EDIT_TYPE, '修改资料记录');
        }

        if (!empty($inputPassword)) {
            //修改密码
            $oldPassword  = $inputPassword['old'] ?? '';
            $newPassword  = $inputPassword['new'] ?? '';
            $new2Password = $inputPassword['new2'] ?? '';
            if (empty($oldPassword)) {
                throw new \ErrorException('请输入旧密码');
            }
            if (empty($newPassword)) {
                throw new \ErrorException('请输入新密码');
            }
            if (strlen($newPassword) < 6) {
                throw new \ErrorException('新密码必须6位以上');
            }
            if ($newPassword != $new2Password) {
                throw new \ErrorException('新密码与确认密码不一致');
            }
            if (!Hash::check($oldPassword, $agent->password)) {
                throw  new \ErrorException('旧密码不正确');
            }
            $agent->password = Hash::make($newPassword);
            Log::createLog(Log::EDIT_TYPE, '修改密码记录');
        }

        if ($agent->save()) {
            return true;
        } else {
            throw new \ErrorException('更新资料失败');
        }
    }

    /**
     * 保存代理区域 add by gui
     * @throws \ErrorException
     */
    public function saveProxyRegion($agentID, $input)
    {
        $RegionId = $input['RegionId'] ?? null;
        if (is_null($RegionId)) {
            return true;
        }
        $ids = [];
        foreach ($RegionId as $str) {
            $idArr = explode(',', $str);
            foreach ($idArr as $id) {
                $ids[] = $id;
                $check = $this->checkHasAgentRegion($id, $agentID);
                if ($check !== true) {
                    continue;
                }

                $info = AgentRegion::where('agent_id', $agentID)->where('proxy_region_id', $id)->first();
                if (empty($info)) {
                    $insArr = [
                        'agent_id'        => $agentID,
                        'proxy_region_id' => $id
                    ];
                    AgentRegion::create($insArr);
                }
            }
        }
        AgentRegion::where('agent_id', $agentID)->whereNotIn('proxy_region_id', $ids)->delete();
    }

    /**
     * 判断是否有其他代理商已代理区域
     * add by gui
     * @param $region_id
     * @param integer $agent_id 排除ID
     * @throws \ErrorException
     */
    public function checkHasAgentRegion($region_id, $agent_id)
    {
        $region      = Region::find($region_id);
        $province_id = $region->province_id ?? 0;
        $city_id     = $region->city_id ?? 0;
        $county_id   = $region->county_id ?? 0;
        $town_id     = $region->town_id ?? 0;
        $level       = $region->level ?? 0;

        //是否有省代理
        $agent = $this->getAgentRegion($province_id, $agent_id, $level);
        if (isset($agent->id)) {
            throw new \ErrorException($region->name . '存在代理商[' . $agent->agent_name . ']');
        }
        //是否有市级代理
        $agent = $this->getAgentRegion($city_id, $agent_id, $level);
        if (isset($agent->id)) {
            throw new \ErrorException($region->name . '存在代理商[' . $agent->agent_name . ']');
        }
        //是否存在县区代理
        $agent = $this->getAgentRegion($county_id, $agent_id, $level);
        if (isset($agent->id)) {
            throw new \ErrorException($region->name . '存在代理商[' . $agent->agent_name . ']');
        }
        //是否存在镇区代理
        $agent = $this->getAgentRegion($town_id, $agent_id, $level);
        if (isset($agent->id)) {
            throw new \ErrorException($region->name . '存在代理商[' . $agent->agent_name . ']');
        }

        //当前

        return true;
    }

    /**
     * 获取代理商等级区域 add by gui
     * @param $region_id
     * @param $agent_id
     * @param $level
     * @return bool
     */
    protected function getAgentRegion($region_id, $agent_id, $level)
    {
        if (empty($region_id)) {
            return false;
        }
//        var_dump($level);
//        var_dump($region_id);
        $agent = Agent::select('agents.id', 'agents.agent_name')->where('agents.status', 1)->where('agents.id', '<>', $agent_id)
            ->join('agent_regions', 'agents.id', '=', 'agent_regions.agent_id')
            ->join('regions', 'regions.id', '=', 'agent_regions.proxy_region_id')
            ->where(function ($query) use ($region_id, $level) {
                if ($level < 4) {
                    $query->where('agent_regions.proxy_region_id', $region_id)
                        ->orWhere(function ($query2) use ($region_id, $level) {
                            $query2->where('regions.area_region', 'like', '%|' . $region_id . '|%')
                                ->where('regions.level', '<=', $level);
                        });
                } else {
                    $query->where('agent_regions.proxy_region_id', $region_id);
                }


            })->first();
//        var_dump($agent);
//        var_dump($region_id);
//        dd($agent);
        return $agent;
    }

    public function allowDelete($id)
    {
        $count = Order::where('agent_id', $id)->count();
        if ($count) {
            return false;
        }

        $count = Bill::where('agent_id', $id)->count();
        if ($count) {
            return false;
        }
        return true;
    }
}
