<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class MemberValidator.
 *
 * @package namespace App\Validators;
 */
class MemberValidator extends LaravelValidator
{
    const  RULE_REG_MEMBER = 'register';//前台注册
    const  RULE_APPROVAL = 'approval';//审批会员
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules
        = [
            ValidatorInterface::RULE_CREATE => [
                'real_name'          => 'required',
                //'wx_account'         => 'required',
                'mobile'             => 'required',
                'birthday'           => 'required',
                'native_region_id'   => 'required|gt:0',
                'resident_region_id' => 'required|gt:0',
                'resident_address'   => 'required',
                'work_type'          => 'required',
                'working_year'       => 'required',
                //'business_channel'   => 'required',
                'status'             => 'required'
            ],
            ValidatorInterface::RULE_UPDATE => [
                'real_name'          => 'required',
                //'wx_account'         => 'required',
                'mobile'             => 'required',
                'birthday'           => 'required',
                'native_region_id'   => 'required|gt:0',
                'resident_region_id' => 'required|gt:0',
                'resident_address'   => 'required',
                'work_type'          => 'required',
                'working_year'       => 'required',
                //'business_channel'   => 'required',
//                'status'             => 'required'
            ],
            self::RULE_REG_MEMBER           => [
                'real_name'        => 'required',
                'mobile'           => 'required',
                //'wx_account'          => 'required',
                'birthday'         => 'required',
                'gender'           => 'required',
            'native_region_id'   => 'required|gt:0',
                'work_type'        => 'required',
                'working_year'     => 'required',
            'resident_region_id' => 'required|gt:0',
                'resident_address' => 'required',
                //'business_channel' => 'required',
            ],
            self::RULE_APPROVAL             => [
                'status' => 'required'
            ]
        ];
    protected $attributes
        = [
            'real_name'          => '真实姓名',
            'mobile'             => '手机号码',
            'wx_name'            => '微信昵称',
            'wx_account'         => '微信号',
            'birthday'           => '出生日期',
            'gender'             => '性别',
            'native_region_id'   => '户籍区域',
            'work_type'          => '工种',
            'working_year'       => '从业年限',
            'resident_region_id' => '常驻区域',
            'resident_address'   => '常驻地址',
            'business_channel'   => '业务渠道',
            'status'             => '状态'
        ];
    protected $messages
        = [
            'native_region_id.gt'   => '籍贯区域 不能为空',
            'resident_region_id.gt' => '常驻区域 不能为空'
        ];
}
