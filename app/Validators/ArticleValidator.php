<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class ArticleValidator.
 *
 * @package namespace App\Validators;
 */
class ArticleValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules
        = [
            ValidatorInterface::RULE_CREATE => [
                'title'       => 'required',
                'push_source' => 'required',
                'view_number' => 'required',
                'content'     => 'required',
                'status'      => 'required',
            ],
            ValidatorInterface::RULE_UPDATE => [
                'title'       => 'required',
                'push_source' => 'required',
                'view_number' => 'required',
                'content'     => 'required',
                'status'      => 'required',
            ],
        ];
    protected $attributes = [
        'title'       => '公告标题',
        'push_source' => '发布来源',
        'view_number' => '浏览次数',
        'content'     => '公告内容',
        'status'      => '状态',
    ];
}
