<?php

namespace App\Repositories;

use App\Entities\ArticleRead;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ArticleRepository;
use App\Entities\Article;
use App\Validators\ArticleValidator;

/**
 * Class ArticleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ArticleRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Article::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return ArticleValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     *
     * 保存机构已读记录 add by gui
     * @param $articleID
     * @param $agentID
     * @return bool
     * @throws \ErrorException
     */
    public function saveAgentRead($articleID, $agentID)
    {
        $articleRead = ArticleRead::where('article_id', $articleID)
            ->where('agent_id', $agentID)
            ->first();
        if (!$articleRead) {
            //新增
            $articleRead = new ArticleRead();
            $articleRead->fill([
                'article_id' => $articleID,
                'agent_id'   => $agentID,
                'is_read'    => 1
            ]);
            $ret = $articleRead->save();
            if ($ret) {
                return true;
            } else {
                throw new \ErrorException('保存已读记录失败');
            }
        }
    }

    public function allowDelete($id)
    {
        $count = ArticleRead::where('article_id',$id)->count();
        if($count){
            return false;
        }
        return true;
    }
}
