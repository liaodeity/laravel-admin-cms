<?php


/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/11/18
 */
/**
 * 用户头像
 * add by gui
 * @param $url
 * @return string
 */
function show_user_image($url)
{
    $default = asset('admin-ui/images/default-user.png');
    return $url ? $url : $default;
}

/**
 * 检查颜色格式是否正确 add by gui
 * @param $val
 * @return bool
 */
function check_color($val)
{
    $pattern = '/^#[0-9|a-f|A-F]{3,6}$/';
    if (preg_match($pattern, $val)) {
        //满足
        return true;
    } else {
        //不满足
        return false;
    }
}

/**
 * 获取版本号 add by gui
 * @return int|string
 */
function get_version()
{
    if (config('app.debug')) {
        return time();
    } else {
        return 'v1.0.3';
    }
}

/**
 * 获取英文列信息 add by gui
 * @param null $count
 * @param null $val
 * @return array
 */
function get_col_number($count = null, $val = null)
{
    $arr = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G',
        'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];
    $col = $arr;
    foreach ($arr as $v) {
        foreach ($arr as $v2) {
            $col[] = $v . $v2;
        }
    }
    $result = [];
    if (is_null($count)) {
        $result = $col;
    } else {
        for ($i = 0; $i < $count; $i++) {
            $result[] = $col[$i];
        }
    }
    if (!is_null($val)) {
        foreach ($result as &$value) {
            $value = $val;
        }
    }

    return $result;
}

/**
 * 获取文件的后缀名，带点号
 * add by gui
 * @param        $file
 * @param string $default
 * @return string
 */
function get_extension($file, $default = '')
{
    $path = pathinfo($file);
    $ext  = $path['extension'] ?? '';
    if ($ext) {
        return '.' . $ext;
    } else {
        return $default ? '.' . $default : '';
    }
}

/**
 * 获取当前用户信息，优先级显示
 * add by gui
 * @param $item
 * @return mixed
 */
function get_format_name($item)
{
    if(isset($item->source_type) && isset($item->source_id)){

    }
    if (isset($item->member_id) && $item->member_id) {
        $info = \App\Entities\Member::find($item->member_id);

        return $info->real_name ?? '';
    }
    if (isset($item->agent_id) && $item->agent_id) {
        $info = \App\Entities\Agent::find($item->agent_id);

        return $info->agent_name ?? '';
    }
    if (isset($item->admin_id) && $item->admin_id) {
        $info = \App\Entities\Admin::find($item->admin_id);

        return $info->nickname ?? '';
    }
    if(isset($item->wx_account_id) && $item->wx_account_id){
        $info  =\App\Entities\WxAccount::find($item->wx_account_id);
        return $info->nickname ?? '';
    }
}

/**
 * 获取键值对配置值 add by gui
 * @param        $key
 * @param string $ind
 * @param bool $html
 * @return array|\Illuminate\Contracts\Translation\Translator|mixed|string|null
 */
function get_item_parameter($key, $ind = 'all', $html = false)
{
    $arr = get_lang_parameter($key);
    if ($ind !== 'all') {
        $text = array_key_exists($ind, $arr) ? $arr[$ind] : null;

        return $html === true ? status_html($text) : $text;
    }

    return $arr;
}

/**
 * 获取无键值对配置数组 add by gui
 * @param $key
 * @return array|\Illuminate\Contracts\Translation\Translator|string|null
 */
function get_lang_parameter($key)
{
    return __('parameter.' . $key);
}

/**
 * 获取不解析HTML的文本转移字 add by gui
 * @param $text
 * @return string
 */
function trans_text($text)
{
    $text = trans($text);

    return strip_tags($text);
}

/**
 * 根据图片ID显示图片
 * add by gui
 * @param $id
 * @return string
 */
function show_picture_to_id($id)
{
    if (empty($id)) {
        return '';
    }
    $url     = '';
    $picture = \App\Entities\Picture::find($id);
    if ($picture) {
        $url = show_picture_url($picture);
    }
    return $url;
}
/**
 * 显示图片URL add by gui
 * @param        $picture
 * @param string $default
 * @return string
 */
function show_picture_url($picture, $default = '')
{

    return isset($picture->path) ? asset($picture->path) : $default;
}

/**
 * 日期访问input add by gui
 * @param $field
 * @return string
 */
function html_date_input($field)
{
    $str = <<<EOT
        <input id="{$field}_start" type="text" name="{$field}_start" class="form-control Wdate-bg " autocomplete="off" onclick="WdatePicker({maxDate: '#F{\$dp.\$D(\\'{$field}_end\\')}'})" style="width:150px;">
        <div class="input-group-append " data-target="#reservationdate" data-toggle="datetimepicker">
                            <div class="input-group-text">至</div>
                        </div>
        <input id="{$field}_end" name="{$field}_end" class="form-control Wdate-bg input-group-append" autocomplete="off" type="text" onclick="WdatePicker({minDate: '#F{\$dp.\$D(\\'{$field}_start\\')}'})" style="width:150px;">
EOT;

    return $str;
}

/**
 * 日志记录内容
 * add by gui
 * @param $type
 * @param $content
 */
function create_logs($type, $content)
{
    $Log = new \App\Entities\Log();
    $ret = $Log->createLog($type, $content);

    return $ret;
}

/**
 * 文件大小单位
 * @param $size
 * @return string
 */
function filesize_formatted($size)
{
    $units = [
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB',
        'EB',
        'ZB',
        'YB',
    ];
    $power = $size > 0 ? floor(log($size, 1024)) : 0;

    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

/**
 * 状态解析标记html显示
 * add by gui
 * @param $text
 * @return array|\Illuminate\Contracts\Translation\Translator|string|null
 */
function status_html($text)
{
    if ($text) {
        //特殊情况
        return trans($text);
    }
    return $text;
}

/**
 * 是否DEBUG add by gui
 * @return \Illuminate\Config\Repository|mixed
 */
function is_debug()
{
    return config('app.debug', false);
}

/**
 * 获取会员登录ID add by gui
 * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
 */
function get_member_id()
{
    return session('login_member_uid', 0);
}

/**
 * 获取代理商登录ID add by gui
 * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
 */
function get_agent_id()
{
    $uid = session('login_agent_uid', 0);
    return $uid;
}

/**
 * 获取后台账号登录ID add by gui
 * @return int
 */
function get_admin_id()
{
    return session('login_admin_uid', 0);
}

/**
 * 设置参数默认值 add by gui
 * @param        $input
 * @param string $default
 */
function input_default(&$input, $default = '', $arr = [null])
{
    foreach ($input as $key => $item) {
        if (substr($key, -3) == '_at'
            || substr($key, -5) == '_date'
            || substr($key, -5) == '_time'
            || $key == 'birthday') {
            continue;//时间日期允许为null
        }
        if (in_array($item, $arr)) {
            $input[$key] = $default;
        }
    }

    return $input;
}

/**
 * 获取配置信息内容 add by gui
 * @param $name
 * @return
 */
function get_config_value($name, $default)
{
    $config = new \App\Entities\Config();
    $value  = $config->getConfig($name, $default);

    return $value;
}


/**
 * 是否超级管理员 add by gui
 * @return bool
 */
function is_super_admin()
{
    return true;
    $uid   = get_admin_id();
    $admin = \App\Entities\Admin::find($uid);
    if (!$admin) {

        return false;
    }
    $super = $admin->isSuperAdmin();

    return $super ? true : false;
}

/**
 * 显示按钮add by gui
 * @param        $permission
 * @param        $route
 * @param string $title
 * @param string $name
 * @return string
 */
function get_auth_show_button($permission, $route, $title = '查看信息', $name = '查看')
{
    $AuthButton = new \App\Libs\AuthHtml();

    return $AuthButton->setPermission($permission)
        ->setRoute($route)
        ->setTitle($title)
        ->setName($name)
        ->html();
}

/**
 * 修改按钮 add by gui
 * @param        $permission
 * @param        $route
 * @param string $title
 * @param string $name
 * @return string
 */
function get_auth_edit_button($permission, $route, $title = '编辑信息', $name = '编辑')
{
    $AuthButton = new \App\Libs\AuthHtml();
    return $AuthButton->setPermission($permission)
        ->setRoute($route)
        ->setTitle($title)
        ->setName($name)
        ->html();
}

/**
 * 删除按钮 add by gui
 * @param        $permission
 * @param        $route
 * @param string $title
 * @param string $name
 * @return string
 */
function get_auth_delete_button($permission, $route, $title = '是否删除？', $name = '删除')
{
    $AuthButton = new \App\Libs\AuthHtml();
    return $AuthButton->setPermission($permission)
        ->setRoute($route)
        ->setTitle($title)
        ->setName($name)
        ->setFunction('delete_confirm_fun')
        ->setClass('btn-danger')
        ->html();
}

/**
 * 提示按钮
 * add by gui
 * @param        $permission
 * @param        $route
 * @param string $title
 * @param string $name
 * @return string
 */
function get_auth_confirm_button($permission, $route, $title = '是否操作？', $name = '确认', $fun = 'confirm_fun')
{
    $AuthButton = new \App\Libs\AuthHtml();
    return $AuthButton->setPermission($permission)
        ->setRoute($route)
        ->setTitle($title)
        ->setName($name)
        ->setFunction($fun)
        ->html();
}

/**
 * 危险警告确认 add by gui
 * @param        $permission
 * @param        $route
 * @param string $title
 * @param string $name
 * @param string $fun
 * @return mixed
 */
function get_auth_danger_button($permission, $route, $title = '是否操作？', $name = '确认', $fun = 'confirm_fun')
{
    $AuthButton = new \App\Libs\AuthHtml();
    return $AuthButton->setPermission($permission)
        ->setRoute($route)
        ->setTitle($title)
        ->setName($name)
        ->setFunction($fun)
        ->setClass('btn-danger')
        ->html();
}

/**
 * 提示按钮
 * add by gui
 * @param        $permission
 * @param        $route
 * @param string $title
 * @param string $name
 * @return string
 */
function get_auth_confirm_button_default($permission, $route, $title = '是否操作？', $name = '确认', $fun = 'confirm_fun')
{
    $AuthButton = new \App\Libs\AuthHtml();
    return $AuthButton->setPermission($permission)
        ->setRoute($route)
        ->setTitle($title)
        ->setName($name)
        ->setFunction($fun)
        ->setClass('btn-default')
        ->html();
}

/**
 * 有权限才显示的内容
 * add by gui
 * @param $permission
 * @param $html
 * @return string
 */
function get_auth_html($permission, $name, $html)
{
    $AuthButton = new \App\Libs\AuthHtml();

    return $AuthButton->setPermission($permission)
        ->setName($name)
        ->setHtml($html)
        ->html();
}

/**
 * 生成更多下拉操作按钮
 * add by gui
 * @param $html
 * @return string
 */
function get_more_button($html)
{
    $more_html = '';
    if ($html) {
        $more_html = '<button type="button" class="btn btn-sm btn-info dropdown-toggle"
                            data-toggle="dropdown" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        ' . $html . '
                    </ul>';
    }
    return $more_html;
}

/**
 * 检测后台人员权限 add by gui
 * @param        $permission
 * @param string $title
 * @return bool
 */
function check_admin_permission($permission, $title = '')
{
    $permission = trim($permission);
    //TODO debug:delete
    if (is_debug()) {
        \Spatie\Permission\Models\Permission::findOrCreate($permission, 'admin');
        $menu = \App\Entities\Menu::where('auth_name', $permission)->first();
        if (!$menu) {
            list($auth, $auth2) = explode(' ', $permission);
            $menu = \App\Entities\Menu::where('auth_name', $auth2)->first();
            $pid  = $menu ? $menu->id : 0;
            if ($title == '') {
                switch ($auth) {
                    case 'edit';
                    case 'update';
                        $title = '修改';
                        break;
                    case 'create':
                    case 'add':
                        $title = '添加';
                        break;
                    case 'del':
                    case 'delete':
                        $title = '删除';
                        break;
                    case 'look':
                    case 'view':
                    case 'show':
                        $title = '查看';
                        break;
                    case 'import':
                        $title = '导入';
                        break;
                    case 'disable':
                        $title = '禁用';
                        break;
                    case 'enable':
                        $title = '启用';
                        break;
                    case 'export':
                        $title = '导出';
                        break;
                    case 'patch_del':
                    case 'patch_delete':
                        $title = '批量删除';
                        break;
                }
            }

            $insArr = [
                'module'    => 'admin',
                'auth_name' => $permission,
                'pid'       => $pid,
                'type'      => \App\Entities\Menu::MENU_TYPE_AUTH,
                'title'     => $title,
                'status'    => 1,
                'sort'      => 0,
                'route_url' => '',
                'icon'      => '',
            ];
            $ret    = \App\Entities\Menu::create($insArr);
        }
    }
    //TODO debug:delete

    $uid   = get_admin_id();
    $admin = \App\Entities\Admin::find($uid);
    if (!$admin) {

        return false;
    }
    $super = $admin->isSuperAdmin();
    if ($super) return true;//超级管理员
    $auth = $admin->hasPermissionTo($permission);
    if ($auth) {
        return true;//拥有权限
    }

    return false;
}

/**
 * 检测后台人员权限 add by gui
 * @param        $permission
 * @param string $title
 * @return bool
 */
function check_agent_permission($permission, $title = '')
{
    $permission = trim($permission);

    $uid   = get_agent_id();
    $agent = \App\Entities\Agent::find($uid);
    if (!$agent) {
        return false;
    }
    $super = $agent->isAgentRole();
    if ($super) return true;//代理商角色

    return false;
}

/**
 * 会员判定权限 add by gui
 * @param        $permission
 * @param string $title
 * @return bool
 */
function check_member_permission($permission, $title = '')
{
    return true;
}

/**
 * 错误AJAX add by gui
 * @param $message
 * @return \Illuminate\Http\JsonResponse
 */
function ajax_error_message($message)
{
    $arr = [
        'error'   => true,
        'message' => $message
    ];

    return response()->json($arr);
}

function mix_build_dist ($path)
{
    $dir = config ('app.debug') ? 'build' : 'dist';

    return mix ($path, $dir);
}
