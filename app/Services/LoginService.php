<?php


namespace App\Services;


use App\Exceptions\BusinessException;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Log;
use App\Models\QrCodeLogin;
use App\Models\ShareUrl;
use App\Models\User;
use App\Models\UserAgent;
use App\Models\UserCompany;
use App\Repositories\ResumeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginService
{
    const ADMIN_TYPE = 1;

    /**
     *  add by gui
     * @param string $username 用户名称
     * @param string $password 密码明文
     * @param int    $type
     * @return bool
     * @throws BusinessException
     */
    public function checkLogin ($username, $password, $type = self::ADMIN_TYPE)
    {
        if (empty($username)) {
            throw new BusinessException('用户名称为空');
        }
        if (empty($password)) {
            throw new BusinessException('密码为空');
        }
        switch ($type) {
            case self::ADMIN_TYPE:
                return $this->checkAdmin ($username, $password);
                break;
            default:
                throw new BusinessException('登录认证方式不存在');
                break;
        }
    }

    /**
     *  add by gui
     * @param string $password 原始密码
     * @return string|null
     */
    public function getEncryptPassword ($password)
    {
        $password = $password ? Hash::make ($password) : null;

        return $password;
    }

    /**
     * 检查是否已经登录 add by gui
     */
    public function checkIsLogin ()
    {
        if (session ()->get ('LOGIN_ADMIN') == 'admin'
            && get_login_user_id ()
        ) {
            return true;
        } else {
            false;
        }
    }

    /**
     * 登录成功，并写入session add by gui
     * @param integer $user_id 企业登录用户ID
     */
    protected function setLoginSession ($user_id)
    {
        session ()->put ('LOGIN_USER_ID', $user_id);
        session ()->put ('LOGIN_ADMIN', 'admin');
        //登录日志
        Log::createLog (Log::LOGIN_TYPE, User::showName ($user_id) . '成功登录了后台', '', $user_id, User::class);
        $user = User::find ($user_id);
        $user->login_count++;
        $user->last_login_at = now ();
        $user->save ();
    }

    /**
     *  add by gui
     * @param string $username
     * @param string $password
     * @return bool
     * @throws BusinessException
     */
    private function checkAdmin (string $username, string $password)
    {
        $user = User::where ('username', $username)->first ();
        if (!$user) {
            throw new BusinessException('账号不存在');
        }
        $e_password = $user->password ?? '';
        if (!Hash::check ($password, $e_password)) {
            throw new BusinessException('账号密码不正确');
        }
        if ($user->status != 1) {
            throw new BusinessException('账号' . $user->statusItem ($user->status) . '，无法进行登录');
        }
        $this->setLoginSession ($user->id);

        return true;
    }

}
