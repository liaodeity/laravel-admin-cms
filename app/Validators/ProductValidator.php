<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class ProductValidator.
 *
 * @package namespace App\Validators;
 */
class ProductValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules
        = [
            ValidatorInterface::RULE_CREATE => [
                'title'           => 'required',
                'model'           => 'required',
                'standard_no'     => 'required',
                'shelf_life'      => 'required',
                'content'         => 'required',
                'card_background' => 'required',
                'video_url'       => 'url',
                'status'          => 'required',

            ],
            ValidatorInterface::RULE_UPDATE => [
                'title'           => 'required',
                'model'           => 'required',
                'standard_no'     => 'required',
                'shelf_life'      => 'required',
                'content'         => 'required',
                'card_background' => 'required',
                'video_url'       => 'url',
                'status'          => 'required',
            ],
        ];
    protected $attributes
        = [
            'title'           => '商品标题',
            'model'           => '产品型号',
            'standard_no'     => '执行标准',
            'shelf_life'      => '保质期',
            'content'         => '商品内容',
            'card_background' => '卡片底色',
            'video_url'       => '商品视频',
            'status'          => '状态',
        ];
}
