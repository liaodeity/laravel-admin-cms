<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class ConfigValidator.
 *
 * @package namespace App\Validators;
 */
class ConfigValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [],
        ValidatorInterface::RULE_UPDATE => [
            'context' => 'required'
        ],
    ];
    protected $attributes = [
        'context' => '配置值'
    ];
}
