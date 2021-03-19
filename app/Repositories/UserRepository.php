<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020-04-28
 */

namespace App\Repositories;


use App\Exceptions\BusinessException;
use App\Models\Log;
use App\Models\User;
use App\Services\LoginService;
use App\Validators\UserValidator;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return User::class;
    }

    public function allowDelete ($id)
    {
        return true;
    }

    /**
     *  add by gui
     * @param $user_id
     * @param $password
     * @return bool|null
     * @throws BusinessException
     */
    public function changePassword ($user_id, $password)
    {
        if (is_null ($password)) {
            return null;
        }

        $user = $this->find ($user_id);
        if (empty($user)) {
            throw new BusinessException('用户不存在');
        }
        $old_pwd  = array_get ($password, 'old_pwd');
        $new_pwd  = array_get ($password, 'new_pwd');
        $new_pwd2 = array_get ($password, 'new_pwd2');
        if ($new_pwd !== $new_pwd2) {
            throw new BusinessException('新密码与确认新密码不一致');
        }
        $LoginService = new LoginService();
        if (Hash::check ($old_pwd, $user->password)) {
            $user->password = $LoginService->getEncryptPassword ($new_pwd);
            if ($user->save ()) {
                Log::createLog (Log::EDIT_TYPE, '修改用户[' . $user->username . ']账号密码', '', $user_id, User::class);

                return true;
            } else {
                throw new BusinessException('保存失败');
            }
        } else {
            throw new BusinessException('原密码不正确');
        }
    }
}
