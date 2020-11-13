<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/27
 */

namespace App\Services;


use App\Entities\Agent;
use App\Entities\Member;
use App\Entities\WxAccount;

class WxAccountService
{
    /**
     * @var WxAccount
     */
    private $wxAccount;

    public function __construct(WxAccount $wxAccount)
    {

        $this->wxAccount = $wxAccount;
    }

    /**
     * 根据OpenId获取微信账号信息 add by gui
     * @param      $openID
     * @param null $accountType
     * @param null $accountID 关联ID[非主键]
     * @return bool|integer
     * @throws \ErrorException
     */
    public function getAccountToOpenID($openID, $accountType = null, $accountID = null)
    {
        if (empty($openID)) {
            throw new \ErrorException('OpenId参数缺失');
        }
        $M = $this->wxAccount->where('openid', $openID);
        if (!is_null($accountType))
            $M = $M->where('account_type', $accountType);
        if (!is_null($accountID))
            $M = $M->where('account_id', $accountID);
        $account = $M->first();
        if (isset($account->id)) {
            return $account;
        } else {
            return false;
        }
    }

    /**
     * 获取微信账号主键id add by gui
     * @param $openID
     * @return bool|integer
     * @throws \ErrorException
     */
    public function getAccountIDToOpenID($openID)
    {
        $account = $this->getAccountToOpenID($openID);
        if (isset($account->id)) {
            return $account->id;
        } else {
            return false;
        }
    }

    /**
     * 检查微信账号是否存在 add by gui
     * @param string $openID
     * @param integer $accountID
     * @param string $accountType
     * @return bool|integer
     */
    public function checkUnique($openID, $accountID = 0, $accountType = '')
    {
        $account = $this->wxAccount->where('openid', $openID)
            ->where('account_id', $accountID)
            ->where('account_type', $accountType)
            ->first();
        if (isset($account->id)) {
            return $account->id;
        } else {
            return false;
        }
    }

    /**
     * 更新或创建微信账号 add by gui
     * @param array $insArr
     * @return bool
     * @throws \ErrorException
     */
    public function createOrUpdateAccount(array $insArr)
    {
        $openID      = $insArr['openid'] ?? '';
        $accountID   = $insArr['account_id'] ?? 0;
        $accountType = $insArr['account_type'] ?? '';
        if (empty($openID)) {
            throw new \ErrorException('缺少OpenId参数');
        }
        $wxAccountID = $this->checkUnique($openID, $accountID, $accountType);
        if ($wxAccountID !== false) {
            $account     = $this->getAccountToOpenID($openID, $accountType, $accountID);
            $wxAccountID = $account->id ?? 0;
        }
        if (!$wxAccountID) {
            //不存在，查询是否有授权获取的未关联记录
            $account     = $this->getAccountToOpenID($openID, '', 0);
            $wxAccountID = $account->id ?? 0;
        }
        if ($wxAccountID) {
            //已存在，进行更新
            $account = WxAccount::find($wxAccountID);
            $account->fill($insArr);
            $ret = $account->save();
            if (!$ret) {
                throw new \ErrorException('微信账号更新失败');
            }
            $insArr['wx_name'] = $account['wx_name'];
        } else {
            //新增
            $ret = $this->wxAccount->create($insArr);
            if (!$ret) {
                throw new \ErrorException('微信账号创建失败');
            }
        }

        //更新微信名称到其他表
        switch ($accountType) {
            case Agent::class:
                $agent          = Agent::find($accountID);
                $agent->wx_name = $insArr['nickname'] ?? '';
                $agent->save();
                break;
            case Member::class:
                $member          = Member::find($accountID);
                $member->wx_name = $insArr['nickname'] ?? '';
                $member->save();
                break;
        }

        return true;
    }
}
