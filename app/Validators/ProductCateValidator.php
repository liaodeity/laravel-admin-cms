<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class ProductCateValidator.
 *
 * @package namespace App\Validators;
 */
class ProductCateValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules
        = [
            ValidatorInterface::RULE_CREATE => [
                'cate_name' => 'required',
                'status'    => 'required',

            ],
            ValidatorInterface::RULE_UPDATE => [
                'cate_name' => 'required',
                'status'    => 'required',

            ],
        ];
    protected $attributes
        = [
            'status'    => '状态',
            'cate_name' => '分类名称',
        ];
}
