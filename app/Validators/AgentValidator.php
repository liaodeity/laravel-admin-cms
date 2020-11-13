<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class AgentValidator.
 *
 * @package namespace App\Validators;
 */
class AgentValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules
        = [
            ValidatorInterface::RULE_CREATE => [
                'username'      => 'required|unique:agents',
                'password'      => 'required',
                'agent_name'    => 'required',
                'contact_name'  => 'required',
                'contact_phone' => 'required|unique:agents',
                'status'        => 'required',
            ],
            ValidatorInterface::RULE_UPDATE => [
                'username'       => 'required',
                'agent_name'     => 'required',
                'contact_name'   => 'required',
                'contact_phone'  => 'required',
                'authorize_date' => 'required_unless:is_forever_authorize,1',
                'status'         => 'required',
            ],
        ];
    protected $attributes
        = [
            'username'             => '用户名',
            'password'             => '密码',
            'agent_name'           => '代理商名称',
            'contact_name'         => '联系人',
            'contact_phone'        => '联系号码',
            'authorize_date'       => '授权时长',
            'is_forever_authorize' => '长期授权',
            'status'               => '状态',
        ];
    protected $messages = [
        'authorize_date.required_unless'=>'授权时长 不能为空或必须勾选长期，二选其一'
    ];
}
