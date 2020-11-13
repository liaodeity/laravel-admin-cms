<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/23
 */

namespace App\Services;

/**
 * add by gui
 * Class LockScreenService
 * @package App\Services
 */
class LockScreenService
{
    protected $type = '';

    /**
     * 锁屏类型
     * @param string $type
     */
    public function setType(string $type): LockScreenService
    {
        $this->type = $type;

        return $this;
    }

    /**
     * 锁定session add by gui
     * @return string
     */
    protected function getSessionKey()
    {
        return $this->type . '_is_lock_screen';
    }

    /**
     * 设置锁屏
     */
    public function setLock()
    {
        session()->put($this->getSessionKey(), 1);
    }

    /**
     * 取消锁屏
     */
    public function cancelLock()
    {
        session()->put($this->getSessionKey(), 0);
    }


    /**
     * 检查是否锁屏
     *  add by gui
     * @return bool
     */
    public function checkIsLock()
    {
        $key = $this->getSessionKey();

        $check = session($key);
        return $check == 1 ? true : false;
    }
}
