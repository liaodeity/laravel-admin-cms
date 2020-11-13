<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class RoleValidator.
 *
 * @package namespace App\Validators;
 */
class RoleValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name'=>'required|unique:auth_infos',
            'desc'=>'required',
            'status'=>'required'
        ],
        ValidatorInterface::RULE_UPDATE => [
            'name'=>'required|unique:auth_infos',
            'desc'=>'required',
            'status'=>'required'
        ],
    ];

    protected $attributes = [
        'name'=>'角色名称',
        'desc'=>'角色说明',
        'status'=>'状态'
    ];
}
