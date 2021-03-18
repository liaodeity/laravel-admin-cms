<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020/4/8
 */

namespace App\Libs;


class IdCard
{
    //成功返回true
    public function validation_filter_id_card ($id_card)
    {
        $id_card = trim ($id_card);
        if (strlen ($id_card) == 18) {
            return $this->idcard_checksum18 ($id_card);
        } elseif ((strlen ($id_card) == 15)) {
            $id_card = $this->idcard_15to18 ($id_card);

            return $this->idcard_checksum18 ($id_card);
        } else {
            return false;
        }
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    protected function idcard_verify_number ($idcard_base)
    {
        if (strlen ($idcard_base) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum           = 0;
        for ($i = 0; $i < strlen ($idcard_base); $i++) {
            $checksum += substr ($idcard_base, $i, 1) * $factor[ $i ];
        }
        $mod           = $checksum % 11;
        $verify_number = $verify_number_list[ $mod ];

        return $verify_number;
    }

    // 将15位身份证升级到18位
    protected function idcard_15to18 ($idcard)
    {
        if (strlen ($idcard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search (substr ($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr ($idcard, 0, 6) .
                    '18' . substr ($idcard, 6, 9);
            } else {
                $idcard = substr ($idcard, 0, 6) .
                    '19' . substr ($idcard, 6, 9);
            }
        }
        $idcard = $idcard . $this->idcard_verify_number ($idcard);

        return $idcard;
    }

    // 18位身份证校验码有效性检查
    protected function idcard_checksum18 ($idcard)
    {
        if (strlen ($idcard) != 18) {
            return false;
        }
        $idcard_base = substr ($idcard, 0, 17);
        if ($this->idcard_verify_number ($idcard_base) != strtoupper (substr ($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取身份证的生日 add by gui
     * @param $idcard
     * @return string
     */
    public function birthday ($idcard)
    {
        $idcard = trim ($idcard);
        $check  = $this->validation_filter_id_card ($idcard);
        if (!$check) {
            return '';
        }
        $len = strlen ($idcard);
        switch ($len) {
            case 15:
                $idcard = $this->idcard_15to18 ($idcard);
                break;
        }
        //解析生日
        $birthday = substr ($idcard, 6, 8);
        $time     = strtotime ($birthday);
        if ($time !== false) {//解析生日
            $birthday = date ('Y-m-d', $time);
            if ($birthday != '1970-01-01') {
                return $birthday;
            }
        }

        return '';
    }

    /**
     * 获取身份证的性别 add by gui
     * @param $idcard
     * @return int
     */
    public function gender ($idcard)
    {
        $idcard = trim ($idcard);
        $check  = $this->validation_filter_id_card ($idcard);
        if (!$check) {
            return 0;
        }
        $len = strlen ($idcard);
        switch ($len) {
            case 15:
                $idcard = $this->idcard_15to18 ($idcard);
                break;
        }
        //性别
        $number = substr ($idcard, -2, 1);
        $ind    = $number % 2;
        $gender = $ind == 1 ? 2 : 3;//2=男，3=女

        return $gender;
    }
}
