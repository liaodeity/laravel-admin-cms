<?php

namespace App\Repositories;

use App\Entities\Agent;
use App\Entities\Bill;
use App\Entities\Log;
use App\Entities\Order;
use App\Entities\RoleInfo;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Admin;
use App\Validators\AdminValidator;
use Spatie\Permission\Models\Role;

/**
 * Class AdminRepositoryEloquent.
 * @package namespace App\Repositories;
 */
class AdminRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model()
    {
        return Admin::class;
    }

    /**
     * Specify Validator class name
     * @return mixed
     */
    public function validator()
    {

        return AdminValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 获取所属角色 add by gui
     * @param null $admin_id
     * @return mixed
     */
    public function getRoleList($admin_id = null)
    {
        $role = Role::all();
        if ($admin_id) {
            $admin = Admin::find($admin_id);
        }else{
            $admin = new Admin();
        }
        foreach ($role as $key => &$item) {
            $info = RoleInfo::where('role_id', $item->id)->first();
            if ($info) {
                $item->title = $info->name;
            } else {
                unset($role{$key});
            }
            $checked = false;
            if ($admin_id) {
                $checked = $admin->hasRole($item->name);
            }
            $item->checked = $checked ? true : false;
        }

        return $role;
    }

    /**
     * 保存账号信息 add by gui
     * @param $adminID
     * @param $input
     * @return bool
     * @throws \ErrorException
     */
    public function savePersonal($adminID, $input)
    {
        $this->makeModel();
        $inputAdmin    = $input['Admin'] ?? [];
        $inputPassword = $input['Password'] ?? [];
        $admin         = $this->model->find($adminID);

        if (!empty($inputAdmin)) {
            $nickname = $inputAdmin['nickname'] ?? '';
            $phone    = $inputAdmin['phone'] ?? '';
            $send_order_tips = $inputAdmin['send_order_tips'] ?? 0;
            //修改资料
            if (empty($nickname)) {
                throw new \ErrorException('请输入管理员名称');
            }
            if (empty($phone)) {
                throw new \ErrorException('请输入联系电话');
            }
            $admin->nickname = $nickname;
            $admin->phone    = $phone;
            $admin->send_order_tips = $send_order_tips;
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
            if (!Hash::check($oldPassword, $admin->password)) {
                throw  new \ErrorException('旧密码不正确');
            }
            $admin->password = Hash::make($newPassword);
            Log::createLog(Log::EDIT_TYPE, '修改密码记录');
        }

        if ($admin->save()) {
            return true;
        } else {
            throw new \ErrorException('更新资料失败');
        }
    }

    public function allowDelete($id)
    {
        $count = Log::where('admin_id', $id)->count();
        if ($count) {
            return false;
        }

        return true;
    }
}
