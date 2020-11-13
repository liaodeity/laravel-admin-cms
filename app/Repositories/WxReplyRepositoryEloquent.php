<?php

namespace App\Repositories;

use App\Entities\WxReplyKeyword;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WxReplyRepository;
use App\Entities\WxReply;
use App\Validators\WxReplyValidator;

/**
 * Class WxReplyRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WxReplyRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WxReply::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return WxReplyValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 更新关键词信息 add by gui
     * @param $replyID
     * @param array $keywords
     * @throws \ErrorException
     */
    public function updateKeywords($replyID, $keywords = [])
    {
        if (empty($keywords)) {
            throw new \ErrorException('关键词不能为空');
        }
        $ids = [];
        foreach ($keywords as $keyword) {
            $info = WxReplyKeyword::where('reply_id', $replyID)->where('keyword', $keyword)->first();
            if (isset($info->id)) {
                $ids[] = $info->id;
                //已存在
                continue;
            }
            $insArr = [
                'reply_id' => $replyID,
                'keyword'  => $keyword
            ];
            $info = WxReplyKeyword::create($insArr);
            if($info){
                $ids[] = $info->id;
            }else{
                throw new \ErrorException('关键词更新失败');
            }
        }
        WxReplyKeyword::where('reply_id', $replyID)->whereNotIn('id', $ids)->delete();
        return true;
    }

    /**
     * 获取多关键词显示格式 add by gui
     * @param WxReply $wxReply
     * @return string
     */
    public function getKeywordView(WxReply $wxReply)
    {
        $keywords = $wxReply->keywords;
        $arr = [];
        foreach ($keywords as $item){
            $arr[] = $item->keyword;
        }
        return implode('|', $arr);
    }
    public function allowDelete($id)
    {
        return true;
    }

}
