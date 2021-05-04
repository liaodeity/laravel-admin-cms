<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2020/3/12
 */
return [
    /**/
    'fail'       => [
        'access' => '访问失败',
    ],
    'tips'       => [
        'no_data'        => '没有数据',
        'no_select_data' => '请选择数据',
    ],
    /*页面Tab标签*/
    'tab'        => [
        'base_info' => '基本信息',
        'back_list' => '返回列表',
        'go_create' => '前往添加',
        'go_edit'   => '前往修改',
        'go_view'   => '前往查看',
    ],
    /*
     * 控制器相关
     * */
    'controller' => [
        'success'     => [
            'get'    => '获取成功',
            'search' => '查询成功',
            'create' => '添加成功',
            'update' => '更新成功',
            'delete' => '删除成功',
        ],
        'delete_fail' => '删除失败',
        'delete_tip'  => '已删除:success条记录，存在:error条记录被使用无法删除',
        'no_super'    => '非超级管理员，无法执行',
    ],
    /**
     * 按钮相关
     */
    'buttons'    => [
        'search'  => '搜 索',
        'login'   => '登 录',
        'show'    => '查看',
        'create'  => '添加',
        'edit'    => '编辑',
        'delete'  => '删除',
        'tips'    => '提示',
        'operate' => '操作',
        'confirm' => '确定',
        'cancel'  => '取消',
        'save_submit'=>'立即提交'
    ],
    /**
     * 确认信息询问
     */
    'confirms'   => [
        'delete' => '确认是否删除:name记录？',
    ],
    /**
     * 列表页面
     */
    'lists'      => [
        'search_info' => '搜索信息',
    ],
    /*
     * 权限认证
     */
    'permission' => [
        'check_error' => '对不起，您没有权限访问'
    ],

    /**
     * 登录页面
     */
    'login'      => [
        'success'      => '登录成功',
        'captcha_fail' => '验证码错误'
    ]
];
