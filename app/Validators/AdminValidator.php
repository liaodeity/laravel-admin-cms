<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class AdminValidator.
 * @package namespace App\Validators;
 */
class AdminValidator extends LaravelValidator
{
    /**
     * Validation Rules
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'username' => 'required|unique:admins',
            'password' => 'required',
            'nickname' => 'required',
            'status'   => 'required',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'username' => 'required',
            'nickname' => 'required',
            'status'   => 'required',
        ],
    ];
    protected $attributes = [
        'username' => '用户名',
        'password' => '密码',
        'nickname' => '管理员名称',
        'status'   => '状态',
    ];
}
