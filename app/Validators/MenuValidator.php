<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class MenuValidator.
 *
 * @package namespace App\Validators;
 */
class MenuValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'title' => 'required'
        ],
        ValidatorInterface::RULE_UPDATE => [],
    ];
    protected $attributes = [
        'title' => '菜单名称'
    ];
}
