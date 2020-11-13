<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2020/3/1
 */

namespace App\Services;


use App\Entities\WxReply;
use App\Entities\WxReplyKeyword;

class WxReplyService
{
    /**
     * 关键字回复查询
     * add by gui
     * @param $keyword
     * @return bool
     */
    public function getKeywordReply($keyword)
    {
        //精准匹配
        $info = WxReply::select('wx_reply.content')
            ->where('wx_reply.status', 1)
            ->where('wx_reply.if_like', 1)
            ->join('wx_reply_keywords', 'wx_reply_keywords.reply_id', '=', 'wx_reply.id')
            ->where('wx_reply_keywords.keyword', $keyword)
            ->orderBy('wx_reply.updated_at', 'DESC')
            ->first();
        if (empty($info)) {
            //非精准匹配
            $info = WxReply::select('wx_reply.content')
                ->where('wx_reply.status', 1)
                ->where('wx_reply.if_like', 2)
                ->join('wx_reply_keywords', 'wx_reply_keywords.reply_id', '=', 'wx_reply.id')
                ->where('wx_reply_keywords.keyword', 'like', '%' . $keyword . '%')
                ->orderBy('wx_reply.updated_at', 'DESC')
                ->first();
        }
        if (isset($info->content)) {
            return $info->content ?? false;
        }
        return false;
    }

    /**
     * 关注回复
     * add by gui
     * @return bool
     */
    public function getSubscribeKeywordReply()
    {
        $info = WxReply::select('wx_reply.content')
            ->where('wx_reply.status', 1)
            ->where('is_subscribe', 1)
            ->orderBy('wx_reply.updated_at', 'DESC')
            ->first();
        if (empty($info)) {
            return false;
        }
        if (isset($info->content)) {
            return $info->content ?? false;
        }
        return false;
    }
}
