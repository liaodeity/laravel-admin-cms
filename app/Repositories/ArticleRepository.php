<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020-04-28
 */

namespace App\Repositories;


use App\Models\Article;
use App\Validators\ArticleValidator;

class ArticleRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return Article::class;
    }

    public function validator ()
    {
        return ArticleValidator::class;
    }

    public function allowDelete ($id)
    {
        return true;
    }
}