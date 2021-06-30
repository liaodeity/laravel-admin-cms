<?php
/*
|-----------------------------------------------------------------------------------------------------------
| laravel-admin-cms [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( MIT )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2020-2021 https://gitee.com/liaodeiy/laravel-admin-cms All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/

namespace App\Validators;


use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Validator;

class BaseValidator
{
    const RULE_CREATE = 'create';
    const RULE_UPDATE = 'update';
    const ROLE_SAVE   = 'save';
    /**
     * @var Validator
     */
    protected $validator;
    /**
     * 验证规则
     * @var array
     */
    protected $rules = [];
    /**
     * 验证字段
     * @var array
     */
    protected $attributes = [];
    /**
     * 验证信息
     * @var array
     */
    protected $messages = [];
    protected $data     = [];
    private   $errors;

    /**
     * BaseValidator constructor.
     */
    public function __construct ()
    {
        $this->validator = new Validator();
    }

    /**
     * 要验证的表单参数数组
     * @param array $data
     * @return $this
     */
    public function with ($data = null)
    {
        if(!is_array ($data)){
            $data = [];
        }
        $this->data = $data;

        return $this;
    }

    /**
     * 进行验证 add by gui
     * @param null $action
     * @return bool
     * @throws BusinessException
     */
    public function passes ($action = null)
    {
        $rules      = array_get ($this->rules, $action, []);
        $messages   = $this->messages;
        $attributes = $this->attributes;
        $validator  = Validator::make ($this->data, $rules, $messages, $attributes);
        if ($validator->fails ()) {
            $this->errors = $validator->getMessageBag ();
            throw new BusinessException($validator->getMessageBag ()->first ());
        }

        return true;
    }

    /**
     * 获取验证规则 add by gui
     * @return array
     */
    public function getRules ()
    {
        return $this->rules;
    }

    /**
     * 设置验证规则 add by gui
     * @param $rules
     * @return BaseValidator
     */
    public function setRules ($rules): BaseValidator
    {
        $this->rules = $rules;

        return $this;
    }
}
