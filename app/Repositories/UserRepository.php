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

    public function formatRequestInput (array $input, $type = null)
    {
        empty_value_null ($input, 'email');

        return $input;
    }

    public function saveMember ($user, $request)
    {
        $input = $request->input ('UserMember');
        $info  = User\UserMember::where ('user_id', $user->id)->first ();
        if ($info) {
            $info->fill ($input);
            $info->save ();
        } else {
            $input['user_id'] = $user->id;
            $info             = User\UserMember::create ($input);
        }
        if (isset($info->id)) {
            Log::createLog (Log::EDIT_TYPE, '修改会员账号', $info->toArray (), $info->id, User\UserMember::class);

            return $info;
        } else {
            return false;
        }
    }

    public function saveAgent ($user, $request)
    {
        $input = $request->input ('UserAgent');
        $info  = User\UserAgent::where ('user_id', $user->id)->first ();
        if ($info) {
            $info->fill ($input);
            $info->save ();
        } else {
            $input['user_id'] = $user->id;
            $info             = User\UserAgent::create ($input);
        }
        if (isset($info->id)) {
            Log::createLog (Log::EDIT_TYPE, '修改代理商账号', $info->toArray (), $info->id, User\UserAgent::class);

            return $info;
        } else {
            return false;
        }
    }

    public function saveAdmin ($user, $request)
    {
        $input = $request->input ('UserAdmin');
        $info  = User\UserAdmin::where ('user_id', $user->id)->first ();
        if ($info) {
            $info->fill ($input);
            $info->save ();
        } else {
            $input['user_id'] = $user->id;
            $info             = User\UserAdmin::create ($input);
        }
        if (isset($info->id)) {
            Log::createLog (Log::EDIT_TYPE, '修改管理员账号', $info->toArray (), $info->id, User\UserAdmin::class);

            return $info;
        } else {
            return false;
        }
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

    public function saveInfo ($user, \Illuminate\Http\Request $request)
    {
        $input = $request->input ('UserInfo');
        $info  = User\UserInfo::where ('user_id', $user->id)->first ();
        if ($info) {
            $info->fill ($input);
            $info->save ();
        } else {
            $input['user_id'] = $user->id;
            $info             = User\UserInfo::create ($input);
        }
        if (isset($info->id)) {
            Log::createLog (Log::EDIT_TYPE, '修改账号资料', $info->toArray (), $info->id, User\UserInfo::class);

            return $info;
        } else {
            return false;
        }
    }
}
