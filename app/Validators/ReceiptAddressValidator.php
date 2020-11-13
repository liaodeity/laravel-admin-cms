<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class ReceiptAddressValidator.
 *
 * @package namespace App\Validators;
 */
class ReceiptAddressValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules
        = [
            ValidatorInterface::RULE_CREATE => [
                'consignee'       => 'required',
                'consignee_phone' => 'required',
                'region_id'       => 'required|gt:0',
                'address'         => 'required'
            ],
            ValidatorInterface::RULE_UPDATE => [
                'consignee'       => 'required',
                'consignee_phone' => 'required',
                'region_id'       => 'required|gt:0',
                'address'         => 'required'
            ],
        ];
    protected $attributes
        = [
            'consignee'       => '收货人',
            'consignee_phone' => '手机号码',
            'region_id'       => '所在地区',
            'address'         => '详细地址'
        ];
    protected $messages = [
        'region_id.gt'=>'所在地区 不能为空'
    ];
}
