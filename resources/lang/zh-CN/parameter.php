<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2019/11/27
 */
/*
|--------------------------------------------------------------------------
| 变量标签定义，通常是下拉框、可选项
|--------------------------------------------------------------------------
| 注意：数组键不能随意修改，有可能会被使用的情况
|
*/
return [

    //性别
    'gender'              => [
        '男',
        '女'
    ],
    //使用状态
    'use_status'          => [
        1 => '使用中',
        2 => '已禁用',
    ],
    //显示状态
    'show_status'         => [
        1 => '显示',
        2 => '隐藏',
    ],
    //备份状态
    'back_status'         => [
        1 => '已完成',
        2 => '备份中',
        3 => '备份失败',
    ],
    //区域等级
    'area_level'          => [
        1 => '省级',
        2 => '市级',
        3 => '县级',
        4 => '镇级',
        5 => '社区',
    ],
    //会员状态
    'member_status'       => [
        1 => '使用中',
        2 => '已禁用',
        3 => '待审核',
        4 => '审核不通过',
    ],
    //从业年限
    'working_year'        => [
        '五年以上',
        '五年以内',
        '三年以内',
        '一年以内',
        '小于半年',
    ],
    //工种
    'work_type'           => [
        '水电师傅',
        '泥工师傅',
        '木工师傅',
        '漆工师傅',
        '项目经理',
        '其他'
    ],
    //商品状态
    'product_status'      => [
        1 => '已发布',
        2 => '草稿',
        4 => '已下架'
    ],
    //是否发展会员
    'is_develop_member'=>[
        1=>'是',
        0=>'否'
    ],
    //是否从账户中扣除
    'is_account_pay'=>[
        1=>'是',
        0=>'否'
    ],
    //是否放卡
    'is_put_card'=>[
        1=>'是',
        0=>'否'
    ],
    //订单状态
    'order_status'        => [
        1 => '交易完成',
        2 => '未付款',
        3 => '已付款未发货',
        5 => '已发货',
        4 => '已取消',
    ],
    //售后状态
    'order_sale_status'   => [
        1 => '处理完成',
        2 => '待处理',
        4 => '取消售后'
    ],
    //佣金状态
    'bill_status'         => [
        1 => '已转账',
        2 => '待转账',
        4 => '已作废'
    ],

    //是否默认
    'is_default'          => [
        1 => '是',
        0 => '否'
    ],
    //是否关注
    'is_subscribe'          => [
        1 => '是',
        0 => '否'
    ],
    //回复是否精确搜索
    'if_like'          => [
        1 => '精确匹配搜索',
        2 => '非精确匹配搜索'
    ],
    //二维码状态
    'order_qrcode_status' => [
        1 => '已生成',
        2 => '未生成',
    ],
    //是否已读
    'is_read'             => [
        1 => '是',
        0 => '否'
    ],
    //
    'pay_trade_type'      => [
        1 => '微信支付',
        2 => '支付宝',
        3 => '线下支付',
        5 => '账户中扣除'
    ],
    //快递常用推荐标识
    'used_express_delivery'=>[
        'SFEXPRESS',//顺丰
        'YTO',//圆通
        'DEPPON',//德邦
        'YUNDA',//韵达
        'STO',//申通
        'HTKY',//汇通快递
        'ZTO',//中通
        'ZTO56',//中通快运
        'BSKY',//百世快运
        'EMS',//EMS
        'CHINAPOST',//邮政包裹
        'TTKDEX',//天天
        'UC56',//优速
        'ZJS',//宅急送
        'JD',//京东
    ],
    //快递可供选择，标识key=接口标识，
    'express_delivery'    => [
        'AAEWEB'        => 'AAE',
        'AOTSD'         => '澳天速运',
        'ANXL'          => '安迅物流',
        'EXFRESH'       => '安鲜达',
        'AJWL'          => '安捷物流',
        'ANTS'          => 'ANTS',
        'ASTEXPRESS'    => '安世通快递',
        'IBUY8'         => '爱拜物流',
        'ADODOXOM'      => '澳多多国际速递',
        'APLUSEX'       => 'Aplus物流',
        'ADAPOST'       => '安达速递',
        'AUSEXPRESS'    => '澳世速递',
        'MAXEEDEXPRESS' => '澳洲迈速快递',
        'ONWAY'         => '昂威物流',
        'ARAMEX'        => 'Aramex',
        'ND56'          => '能达',
        'DHL'           => 'DHL国内件',
        'DHL_EN'        => 'DHL国际件',
        'DPEX'          => 'DPEX',
        'EFSPOST'       => '平安快递',
        'DEXP'          => 'D速',
        'CHINZ56'       => '秦远物流',
        'EMS'           => 'EMS',
        'QCKD'          => '全晨',
        'EWE'           => 'EWE',
        'QFKD'          => '全峰',
        'FEDEX'         => '联邦快递',
        'APEX'          => '全一',
        'FEDEXIN'       => 'FedEx国际',
        'RFD'           => '如风达',
        'PCA'           => 'PCA',
        'SFC'           => '三态',
        'TNT'           => 'TNT',
        'STO'           => '申通',
        'UPS'           => 'UPS',
        'SFWL'          => '盛丰',
        'ANJELEX'       => '安捷快递',
        'SHENGHUI'      => '盛辉',
        'ANE'           => '安能',
        'SDEX'          => '顺达快递',
        'ANEEX'         => '安能快递',
        'SFEXPRESS'     => '顺丰',
        'ANXINDA'       => '安信达',
        'SUNING'        => '苏宁',
        'EES'           => '百福东方',
        'SURE'          => '速尔',
        'HTKY'          => '汇通快递',
        'HOAU'          => '天地华宇',
        'BSKY'          => '百世快运',
        'TTKDEX'        => '天天',
        'FLYWAYEX'      => '程光',
        'VANGEN'        => '万庚',
        'DTW'           => '大田',
        'WANJIA'        => '万家物流',
        'DEPPON'        => '德邦',
        'EWINSHINE'     => '万象',
        'GCE'           => '飞洋',
        'GZWENJIE'      => '文捷航空',
        'PHOENIXEXP'    => '凤凰',
        'XBWL'          => '新邦',
        'FTD'           => '富腾达',
        'XFEXPRESS'     => '信丰',
        'GSD'           => '共速达',
        'BROADASIA'     => '亚风',
        'GTO'           => '国通',
        'YIEXPRESS'     => '宜送',
        'BLACKDOG'      => '黑狗',
        'QEXPRESS'      => '易达通',
        'HENGLU'        => '恒路',
        'ETD'           => '易通达',
        'HYE'           => '鸿远',
        'UC56'          => '优速',
        'HQKY'          => '华企',
        'CHINAPOST'     => '邮政包裹',
        'JOUST'         => '急先达',
        'YFHEX'         => '原飞航',
        'TMS'           => '加运美',
        'YTO'           => '圆通',
        'JIAJI'         => '佳吉',
        'YADEX'         => '源安达',
        'JIAYI'         => '佳怡',
        'YCGWL'         => '远成',
        'KERRY'         => '嘉里物流',
        'YFEXPRESS'     => '越丰',
        'HREX'          => '锦程快递',
        'YTEXPRESS'     => '运通',
        'PEWKEE'        => '晋越',
        'YUNDA'         => '韵达',
        'JD'            => '京东',
        'ZJS'           => '宅急送',
        'KKE'           => '京广',
        'ZMKMEX'        => '芝麻开门',
        'JIUYESCM'      => '九曳',
        'COE'           => '中国东方',
        'KYEXPRESS'     => '跨越速运',
        'CRE'           => '中铁快运',
        'FASTEXPRESS'   => '快捷',
        'ZTKY'          => '中铁物流',
        'BLUESKY'       => '蓝天',
        'ZTO'           => '中通',
        'LTS'           => '联昊通',
        'LBEX'          => '龙邦',
        'ZTO56'         => '中通快运',
        'CNPL'          => '中邮',
        'YIMIDIDA'      => '壹米滴答',
        'PJKD'          => '品骏快递',
        'RRS'           => '日日顺物流',
        'YXWL'          => '宇鑫物流',
        'INTMAIL'       => '邮政国际包裹',
        'DJ56'          => '东骏快捷',
        'FEDEX_GJ'      => '联邦快递国际',
        'PEISI'         => '配思航宇',
        'AYCA'          => '澳邮专线(澳邮中国快运)',
        'BDT'           => '八达通',
        'CITY100'       => '城市100',
        'CJKD'          => '城际快递',
        'D4PX'          => '递四方速递',
        'FKD'           => '飞康达',
        'GTSD'          => '高铁速递',
        'HQSY'          => '环球速运',
        'HYLSD'         => '好来运快递',
        'JAD'           => '捷安达',
        'JTKD'          => '捷特快递',
        'JGWL'          => '景光物流',
        'MB'            => '民邦快递',
        'MK'            => '美快',
        'MLWL'          => '明亮物流',
        'PADTF'         => '平安达腾飞快递',
        'PANEX'         => '泛捷快递',
        'QRT'           => '全日通快递',
        'QXT'           => '全信通',
        'RFEX'          => '瑞丰速递',
        'SAD'           => '赛澳递',
        'SAWL'          => '圣安物流',
        'SDWL'          => '上大物流',
        'ST'            => '速通物流',
        'STWL'          => '速腾快递',
        'SUBIDA'        => '速必达物流',
        'WJK'           => '万家康',
        'XJ'            => '新杰物流',
        'ZENY'          => '增益快递',
        'ZYWL'          => '中邮物流',
        'HEMA'          => '河马动力',
        'AOL'           => '澳通速递',
        'GLS'           => 'GLS',
        'IADLSQDYZ'     => '安的列斯群岛邮政',
        'IADLYYZ'       => '澳大利亚邮政',
        'IAEBNYYZ'      => '阿尔巴尼亚邮政',
        'IAEJLYYZ'      => '阿尔及利亚邮政',
        'IAFHYZ'        => '阿富汗邮政',
        'IAGLYZ'        => '安哥拉邮政',
        'IAGTYZ'        => '阿根廷邮政',
        'IAJYZ'         => '埃及邮政',
        'IALBYZ'        => '阿鲁巴邮政',
        'IALQDYZ'       => '奥兰群岛邮政',
        'IALYYZ'        => '阿联酋邮政',
        'IAMYZ'         => '阿曼邮政',
        'IASBJYZ'       => '阿塞拜疆邮政',
        'IASEBYYZ'      => '埃塞俄比亚邮政',
        'IASNYYZ'       => '爱沙尼亚邮政',
        'IASSDYZ'       => '阿森松岛邮政',
        'IBCWNYZ'       => '博茨瓦纳邮政',
        'IBDLGYZ'       => '波多黎各邮政',
        'IBDYZ'         => '冰岛邮政',
        'IBELSYZ'       => '白俄罗斯邮政',
        'IBHYZ'         => '波黑邮政',
        'IBJLYYZ'       => '保加利亚邮政',
        'IBJSTYZ'       => '巴基斯坦邮政',
        'IBLNYZ'        => '黎巴嫩邮政',
        'IBLSD'         => '便利速递',
        'IBLWYYZ'       => '玻利维亚邮政',
        'IBLYZ'         => '巴林邮政',
        'IBMDYZ'        => '百慕达邮政',
        'IBOLYZ'        => '波兰邮政',
        'IBTD'          => '宝通达',
        'IBYB'          => '贝邮宝',
        'ICKY'          => '出口易',
        'IDFWL'         => '达方物流',
        'IDGYZ'         => '德国邮政',
        'IE'            => '爱尔兰邮政',
        'IEGDEYZ'       => '厄瓜多尔邮政',
        'IELSYZ'        => '俄罗斯邮政',
        'IELTLYYZ'      => '厄立特里亚邮政',
        'IFTWL'         => '飞特物流',
        'IGDLPDEMS'     => '瓜德罗普岛EMS',
        'IGDLPDYZ'      => '瓜德罗普岛邮政',
        'IGJESD'        => '俄速递',
        'IGLBYYZ'       => '哥伦比亚邮政',
        'IGLLYZ'        => '格陵兰邮政',
        'IGSDLJYZ'      => '哥斯达黎加邮政',
        'IHGYZ'         => '韩国邮政',
        'IHHWL'         => '华翰物流',
        'IHLY'          => '互联易',
        'IHSKSTYZ'      => '哈萨克斯坦邮政',
        'IHSYZ'         => '黑山邮政',
        'IJBBWYZ'       => '津巴布韦邮政',
        'IJEJSSTYZ'     => '吉尔吉斯斯坦邮政',
        'IJKYZ'         => '捷克邮政',
        'IJNYZ'         => '加纳邮政',
        'IJPZYZ'        => '柬埔寨邮政',
        'IKNDYYZ'       => '克罗地亚邮政',
        'IKNYYZ'        => '肯尼亚邮政',
        'IKTDWEMS'      => '科特迪瓦EMS',
        'IKTDWYZ'       => '科特迪瓦邮政',
        'IKTEYZ'        => '卡塔尔邮政',
        'ILBYYZ'        => '利比亚邮政',
        'ILKKD'         => '林克快递',
        'ILMNYYZ'       => '罗马尼亚邮政',
        'ILSBYZ'        => '卢森堡邮政',
        'ILTWYYZ'       => '拉脱维亚邮政',
        'ILTWYZ'        => '立陶宛邮政',
        'ILZDSDYZ'      => '列支敦士登邮政',
        'IMEDFYZ'       => '马尔代夫邮政',
        'IMEDWYZ'       => '摩尔多瓦邮政',
        'IMETYZ'        => '马耳他邮政',
        'IMJLGEMS'      => '孟加拉国EMS',
        'IMLGYZ'        => '摩洛哥邮政',
        'IMLQSYZ'       => '毛里求斯邮政',
        'IMLXYEMS'      => '马来西亚EMS',
        'IMLXYYZ'       => '马来西亚邮政',
        'IMQDYZ'        => '马其顿邮政',
        'IMTNKEMS'      => '马提尼克EMS',
        'IMTNKYZ'       => '马提尼克邮政',
        'IMXGYZ'        => '墨西哥邮政',
        'INFYZ'         => '南非邮政',
        'INRLYYZ'       => '尼日利亚邮政',
        'INWYZ'         => '挪威邮政',
        'IPTYYZ'        => '葡萄牙邮政',
        'IQQKD'         => '全球快递',
        'IQTWL'         => '全通物流',
        'ISDYZ'         => '苏丹邮政',
        'ISEWDYZ'       => '萨尔瓦多邮政',
        'ISEWYYZ'       => '塞尔维亚邮政',
        'ISLFKYZ'       => '斯洛伐克邮政',
        'ISLWNYYZ'      => '斯洛文尼亚邮政',
        'ISNJEYZ'       => '塞内加尔邮政',
        'ISPLSYZ'       => '塞浦路斯邮政',
        'ISTALBYZ'      => '沙特阿拉伯邮政',
        'ITEQYZ'        => '土耳其邮政',
        'ITGYZ'         => '泰国邮政',
        'ITLNDHDBGE'    => '特立尼达和多巴哥EMS',
        'ITNSYZ'        => '突尼斯邮政',
        'ITSNYYZ'       => '坦桑尼亚邮政',
        'IWDMLYZ'       => '危地马拉邮政',
        'IWGDYZ'        => '乌干达邮政',
        'IWKLEMS'       => '乌克兰EMS',
        'IWKLYZ'        => '乌克兰邮政',
        'IWLGYZ'        => '乌拉圭邮政',
        'IWLYZ'         => '文莱邮政',
        'IWZBKSTEMS'    => '乌兹别克斯坦EMS',
        'IWZBKSTYZ'     => '乌兹别克斯坦邮政',
        'IXBYYZ'        => '西班牙邮政',
        'IXFLWL'        => '小飞龙物流',
        'IXGLDNYYZ'     => '新喀里多尼亚邮政',
        'IXJPEMS'       => '新加坡EMS',
        'IXJPYZ'        => '新加坡邮政',
        'IXLYYZ'        => '叙利亚邮政',
        'IXLYZ'         => '希腊邮政',
        'IXPSJ'         => '夏浦世纪',
        'IXPWL'         => '夏浦物流',
        'IXXLYZ'        => '新西兰邮政',
        'IXYLYZ'        => '匈牙利邮政',
        'IYDLYZ'        => '意大利邮政',
        'IYDNXYYZ'      => '印度尼西亚邮政',
        'IYDYZ'         => '印度邮政',
        'IYGYZ'         => '英国邮政',
        'IYLYZ'         => '伊朗邮政',
        'IYMNYYZ'       => '亚美尼亚邮政',
        'IYMYZ'         => '也门邮政',
        'IYNYZ'         => '越南邮政',
        'IYSLYZ'        => '以色列邮政',
        'IYTG'          => '易通关',
        'IYWWL'         => '燕文物流',
        'IZBLTYZ'       => '直布罗陀邮政',
        'IZLYZ'         => '智利邮政',
        'JP'            => '日本邮政',
        'NL'            => '荷兰邮政',
        'ONTRAC'        => 'ONTRAC',
        'QQYZ'          => '全球邮政',
        'RDSE'          => '瑞典邮政',
        'SWCH'          => '瑞士邮政',
        'ANGUILAYOU'    => '安圭拉邮政',
        'APAC'          => 'APAC',
        'USPS'          => 'USPS美国邮政',
        'YAMA'          => '日本大和运输(Yamato)',
        'YODEL'         => 'YODEL',
        'YUEDANYOUZ'    => '约旦邮政',
        'AT'            => '奥地利邮政',
        'CAE'           => '民航',
        'EUASIA'        => '欧亚专线',
        'AMAZON'        => '亚马逊',
        'AOMENYZ'       => '澳门邮政',
        'CCES'          => 'CCES快递',
        'BHGJ'          => '贝海国际',
        'BQXHM'         => '北青小红帽',
        'BFAY'          => '八方安运',
        'HOTSCM'        => '鸿桥供应链',
        'CSCY'          => '长沙创一',
        'CDSTKY'        => '成都善途速运',
        'CTG'           => '联合运通',
        'GD'            => '冠达',
        'GDEMS'         => '广东邮政',
        'HFWL'          => '汇丰物流',
        'HPTEX'         => '海派通物流公司',
        'hq568'         => '华强物流',
        'HXWL'          => '豪翔物流',
        'HXLWL'         => '华夏龙物流',
        'SBWL'          => '盛邦物流',
        'NF'            => '南方',
        'TAIWANYZ'      => '台湾邮政',
        'SDEZ'          => '速递e站',
        'UEQ'           => 'UEQ Express',
        'XCWL'          => '迅驰物流',
        'YDH'           => '义达国际物流',
        'XYT'           => '希优特',
        'YUNDX'         => '运东西',
        'YXKD'          => '亿翔快递',
        'ZHQKD'         => '汇强快递',
        'ZTE'           => '众通快递',
        'ACS'           => 'ACS雅仕快递',
        'ADP'           => 'ADP Express Tracking',
        'AUSTRALIA'     => 'Australia Post Tracking',
        'BEL'           => '比利时邮政',
        'BHT'           => 'BHT快递',
        'BILUYOUZHE'    => '秘鲁邮政',
        'BR'            => '巴西邮政',
        'BUDANYOUZH'    => '不丹邮政',
        'DPD'           => 'DPD',
        'DK'            => '丹麦邮政',
        'GJEYB'         => '国际e邮宝',
        'ESHIPPER'      => 'EShipper',
        'BCWELT'        => 'BCWELT',
        'BN'            => '笨鸟国际',
        'UEX'           => 'UEX',
        'ZY_AG'         => '爱购转运',
        'ZY_AOZ'        => '爱欧洲',
        'CA'            => '加拿大邮政',
        'ZY_AXO'        => 'AXO',
        'ZY_AZY'        => '澳转运',
        'ZY_BDA'        => '八达网',
        'ZY_BEE'        => '蜜蜂速递',
        'ZY_BH'         => '贝海速递',
        'ZY_BL'         => '百利快递',
        'ZY_BM'         => '斑马物流',
        'ZY_BOZ'        => '败欧洲',
        'ZY_BT'         => '百通物流',
        'ZY_BYECO'      => '贝易购',
        'ZY_CM'         => '策马转运',
        'ZY_CTM'        => '赤兔马转运',
        'ZY_CUL'        => 'CUL中美速递',
        'ZY_DGHT'       => '德国海淘之家',
        'ZY_DYW'        => '德运网',
        'ZY_EFS'        => 'EFS POST',
        'ZY_ESONG'      => '宜送转运',
        'ZY_ETD'        => 'ETD',
        'ZY_FD'         => '飞碟快递',
        'ZY_FG'         => '飞鸽快递',
        'ZY_FLSD'       => '风雷速递',
        'ZY_FX'         => '风行快递',
        'ZY_HC'         => '皓晨快递',
        'ZY_HCYD'       => '皓晨优递',
        'ZY_HDB'        => '海带宝',
        'ZY_HFMZ'       => '汇丰美中速递',
        'ZY_HJSD'       => '豪杰速递',
        'ZY_HTAO'       => '360hitao转运',
        'ZY_HTCUN'      => '海淘村',
        'ZY_HTKE'       => '365海淘客',
        'ZY_HTONG'      => '华通快运',
        'ZY_HXKD'       => '海星桥快递',
        'ZY_HXSY'       => '华兴速运',
        'ZY_HYSD'       => '海悦速递',
        'ZY_JA'         => '君安快递',
        'ZY_JD'         => '时代转运',
        'ZY_JDKD'       => '骏达快递',
        'ZY_JDZY'       => '骏达转运',
        'ZY_JH'         => '久禾快递',
        'ZY_JHT'        => '金海淘',
        'ZY_LBZY'       => '联邦转运FedRoad',
        'ZY_LPZ'        => '领跑者快递',
        'ZY_LX'         => '龙象快递',
        'ZY_LZWL'       => '量子物流',
        'ZY_MBZY'       => '明邦转运',
        'ZY_MGZY'       => '美国转运',
        'ZY_MJ'         => '美嘉快递',
        'ZY_MST'        => '美速通',
        'ZY_MXZY'       => '美西转运',
        'ZY_MZ'         => '168 美中快递',
        'ZY_OEJ'        => '欧e捷',
        'ZY_OZF'        => '欧洲疯',
        'ZY_OZGO'       => '欧洲GO',
        'ZY_QMT'        => '全美通',
        'ZY_QQEX'       => 'QQ-EX',
        'ZY_RDGJ'       => '润东国际快线',
        'ZY_RT'         => '瑞天快递',
        'ZY_RTSD'       => '瑞天速递',
        'ZY_SCS'        => 'SCS国际物流',
        'ZY_SDKD'       => '速达快递',
        'ZY_SFZY'       => '四方转运',
        'ZY_SOHO'       => 'SOHO苏豪国际',
        'ZY_SONIC'      => 'Sonic-Ex速递',
        'ZY_ST'         => '上腾快递',
        'ZY_TCM'        => '通诚美中快递',
        'ZY_TJ'         => '天际快递',
        'ZY_TM'         => '天马转运',
        'ZY_TN'         => '滕牛快递',
        'ZY_TPAK'       => 'TrakPak',
        'ZY_TPY'        => '太平洋快递',
        'ZY_TSZ'        => '唐三藏转运',
        'ZY_TTHT'       => '天天海淘',
        'ZY_TWC'        => 'TWC转运世界',
        'ZY_TX'         => '同心快递',
        'ZY_TY'         => '天翼快递',
        'ZY_TZH'        => '同舟快递',
        'ZY_UCS'        => 'UCS合众快递',
        'ZY_WDCS'       => '文达国际DCS',
        'ZY_XC'         => '星辰快递',
        'ZY_XDKD'       => '迅达快递',
        'ZY_XDSY'       => '信达速运',
        'ZY_XF'         => '先锋快递',
        'ZY_XGX'        => '新干线快递',
        'ZY_XIYJ'       => '西邮寄',
        'ZY_XJ'         => '信捷转运',
        'ZY_YGKD'       => '优购快递',
        'ZY_YJSD'       => '友家速递(UCS)',
        'ZY_YPW'        => '云畔网',
        'ZY_YQ'         => '云骑快递',
        'ZY_YQWL'       => '一柒物流',
        'ZY_YSSD'       => '优晟速递',
        'ZY_YSW'        => '易送网',
        'ZY_YTUSA'      => '运淘美国',
        'ZY_ZCSD'       => '至诚速递',
        'DANNIAO'       => '丹鸟快递',
        'CJGJ'          => '长江国际快递',

    ]
];
