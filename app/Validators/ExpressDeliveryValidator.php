<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class ExpressDeliveryValidator.
 * @package namespace App\Validators;
 */
class ExpressDeliveryValidator extends LaravelValidator
{
    /**
     * Validation Rules
     * @var array
     */
    protected $rules
        = [
            ValidatorInterface::RULE_CREATE => [
                'name' => 'required',
                'sort' => 'integer',
            ],
            ValidatorInterface::RULE_UPDATE => [
                'name' => 'required',
                'sort' => 'integer',
            ],
        ];
    protected $attributes
        = [
            'name' => '快递名称',
            'sort' => '排序',
        ];
}
