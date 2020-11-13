<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class WxReplyValidator.
 *
 * @package namespace App\Validators;
 */
class WxReplyValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'content'=>'required'
        ],
        ValidatorInterface::RULE_UPDATE => [
            'content'=>'required'
        ],
    ];
    protected $attributes = [
        'content'=>'回复内容'
    ];
}
