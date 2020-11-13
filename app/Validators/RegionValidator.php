<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class RegionValidator.
 * @package namespace App\Validators;
 */
class RegionValidator extends LaravelValidator
{
    /**
     * Validation Rules
     * @var array
     */
    protected $rules
        = [
            ValidatorInterface::RULE_CREATE => [
                'level'  => 'required',
                'name'   => 'required',
                'status' => 'required',
            ],
            ValidatorInterface::RULE_UPDATE => [
//                'level'  => 'required',
                'name'   => 'required',
                'status' => 'required',
            ],
        ];
    protected $attributes
        = [
            'level'  => '区域级别',
            'name'   => '区域名称',
            'status' => '状态',
        ];
}
