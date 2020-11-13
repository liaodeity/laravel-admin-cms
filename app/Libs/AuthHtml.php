<?php
/**
 * User: gui
 * Date: 2019/12/10
 */

namespace App\Libs;

/**
 * 按钮等权限内容权限
 * Class AuthHtml
 * @package App\Libs
 */
class AuthHtml
{
    //后台
    const ADMIN = 'admin';
    //代理商
    const AGENT = 'agent';
    //会员
    const MEMBER = 'member';
    /**
     * @var string
     */
    private $authType = '';
    private $permission = '';
    private $route = '';
    private $title = '';
    private $name = '';
    private $function = 'dialog_fun';
    private $html = null;
    private $class = 'btn-info';

    public function __construct()
    {
        $this->setAuthType(AUTH_SYSTEM_TYPE);
    }

    /**
     * @param string $authType
     */
    private function setAuthType(string $authType): void
    {
        $this->authType = $authType;
    }

    /**
     * @param string $permission
     * @return AuthHtml
     */
    public function setPermission(string $permission): AuthHtml
    {
        $this->permission = $permission;

        return $this;
    }


    /**
     * @param string $route
     * @return AuthHtml
     */
    public function setRoute(string $route): AuthHtml
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @param string $title
     * @return AuthHtml
     */
    public function setTitle(string $title): AuthHtml
    {
        $this->title = $title;

        return $this;
    }


    /**
     * @param string $name
     * @return AuthHtml
     */
    public function setName(string $name): AuthHtml
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $class
     * @return AuthHtml
     */
    public function setClass(string $class): AuthHtml
    {
        $this->class = $class;
        return $this;
    }
    /**
     * @param string $html
     * @return AuthHtml
     */
    public function setHtml(string $html): AuthHtml
    {

        $this->html = is_null($html) ? '' : $html;

        return $this;
    }


    /**
     * @param mixed $function
     * @return AuthHtml
     */
    public function setFunction(string $function): AuthHtml
    {
        $this->function = $function;

        return $this;
    }

    /**
     * 检查是否有权限 add by gui
     * @return bool
     */
    private function checkAuth()
    {
        $check = false;
        switch ($this->authType) {
            case self::ADMIN:
                $check = check_admin_permission($this->permission, $this->name);
                break;
            case self::AGENT:
                $check = check_agent_permission($this->permission, $this->name);
                break;
            case self::MEMBER:
                $check = check_member_permission($this->permission, $this->name);
                break;
            default:
                abort(403,'缺少权限认证标识');
                break;
        }

        return $check;
    }

    /**
     * 默认按钮样式 add by gui
     */
    private function defaultButton()
    {
        switch ($this->function) {
            case 'confirm_fun':
            case 'delete_confirm_fun':
                $this->html = "<button type=\"button\" onclick=\"{$this->function}('" . $this->title . "','" . $this->route . "','" . $this->title . "')\" class=\"btn btn-sm {$this->class}\">" . $this->name . "</button>";
                break;
            default:
                $this->html = "<button type=\"button\" onclick=\"{$this->function}('" . $this->title . "','" . $this->route . "')\" class=\"btn btn-sm {$this->class}\">" . $this->name . "</button>";
        }
    }

    /**
     * 返回html内容 add by gui
     * @return string
     */
    public function html()
    {
        if (!$this->checkAuth()) {
            return '';//无权限
        }
        if (is_null($this->html)) {
            //无HTML内容，使用默认按钮
            $this->defaultButton();
        }

        return $this->html;
    }

}
