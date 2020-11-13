<?php

namespace App\Repositories;

use App\Entities\RoleInfo;
use App\Libs\ArrayHelper;
use function EasyWeChat\Kernel\data_to_array;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\MenuRepository;
use App\Entities\Menu;
use App\Validators\MenuValidator;

/**
 * Class MenuRepositoryEloquent.
 * @package namespace App\Repositories;
 */
class MenuRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model()
    {
        return Menu::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 获取菜单信息
     */
    public function getMenuList()
    {
        //一级菜单
        $list = $this->orderBy('sort', 'asc')->findWhere([
            'status' => 1,
            'pid'    => 0,
            'type'   => Menu::MENU_TYPE_MENU,
        ])->all();
        foreach ($list as $key => $item) {
            //二级菜单
            $child = $this->orderBy('sort', 'asc')->findWhere([
                'status' => 1,
                'pid'    => $item->id,
                'type'   => Menu::MENU_TYPE_MENU,
            ])->all();
            if ($child) {
                $list{$key}->child = $child;
                //三级菜单
                foreach ($child as $key2 => &$item2) {
                    $child2 = $this->orderBy('sort', 'asc')->findWhere([
                        'status' => 1,
                        'pid'    => $item2->id,
                        'type'   => Menu::MENU_TYPE_MENU,
                    ])->all();
//                    $list{$key}->child{$key2}  =[];
                    if ($child2 && count($child2) > 0) {
//                        var_dump($list{$key}->child);
//                        $list{$key}->child{$key} = $child2;
//                        $list{$key}->child2[$key2] = $child2;
                        $item2->child = $child2;
                    }
                }
            }
        }
//        dd($list);
        return $list;
    }

    /**
     * 获取权限列表
     * @return array
     */
    public function getAuthArrayList()
    {
        $list = $this->orderBy('sort', 'asc')->findWhere(['status' => 1,])->toArray();

        $list = ArrayHelper::listToTree($list);

        return $list;
    }

    /**
     * 获取权限的Node数组 add by gui
     * @param null   $id
     * @param string $module
     * @return false|string
     */
    public function getAuthArrayNodes($id = null, $module = 'admin')
    {
        $list = $this->orderBy('sort', 'asc')->findWhere(['status' => 1,'module'=>$module], [
            'id',
            'auth_name',
            'type',
            'pid AS pId',
            'title AS name',
        ]);
        $arr  = [];
        foreach ($list as $item) {
            $arr[$item->pId] = 1;
        }
        $checkArr = [];
        if ($id) {
            $roleInfo = RoleInfo::find($id);
            if ($roleInfo->role_value) {
                $checkArr = explode(',', $roleInfo->role_value);
            }
        }
        foreach ($list as &$item) {
            if (isset($arr[$item->id])) $item->open = true;
            if(in_array($item->id, $checkArr)) $item->checked = true;
        }
        //$list     = ArrayHelper::listToTree ($list, 'id', 'pId');
        $auth_str = json_encode($list, JSON_UNESCAPED_UNICODE);

        return $auth_str;
    }

}
