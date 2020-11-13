<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Picture.
 *
 * @package namespace App\Entities;
 */
class Picture extends Model implements Transformable
{
    use TransformableTrait;
    const TEMP_STATUS   = 0;
    const ACTIVE_STATUS = 1;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'url', 'title', 'md5', 'sha1', 'status', 'picture_id', 'picture_type'
    ];

    /**
     * 检查是否存在图片 add by gui
     * @param $md5
     * @param $sha1
     * @param $picture_id
     * @param $picture_type
     * @return mixed
     */
    public function checkUnique ($md5, $sha1, $picture_id, $picture_type)
    {
        $info = $this->where ('md5', $md5)
            ->where ('sha1', $sha1)
            ->where ('picture_id', $picture_id)
            ->where ('picture_type', $picture_type)
            ->first ();
        if (isset($info->id)) {
            return $info;
        }

        $info = $this->where ('md5', $md5)->where ('sha1', $sha1)->first ();
        if (isset($info->id)) {
            return $info;
        }
    }

    /**
     * 保存图片 add by gui
     * @param        $path
     * @param        $title
     * @param int    $status
     * @param int    $picture_id
     * @param string $picture_type
     * @return mixed
     * @throws \ErrorException
     */
    public function addPicture ($path, $title, $status = 0, $picture_id = 0, $picture_type = '')
    {
        $md5  = md5_file ($path);
        $sha1 = sha1_file ($path);
        $info = $this->checkUnique ($md5, $sha1, $picture_id, $picture_type);
        if (isset($info->id)) {
            if ($info->picture_id != $picture_id && $info->picture_type != $picture_type) {
                //关联来源不同，复制一条记录
                $insArr = [
                    'path'  => $info->path,
                    'url'   => $info->url,
                    'title' => $info->info,
                    'md5'   => $info->md5,
                    'sha1'  => $info->sha1
                ];
                unset($insArr['id'], $insArr['created_at'], $insArr['updated_at']);

                $insArr['picture_id']   = $picture_id;
                $insArr['picture_type'] = $picture_type;
                $insArr['title']        = $title;
                $insArr['status']       = $status;
                $info                   = $this->create ($insArr);
                if (!isset($info->id)) {
                    throw new \ErrorException('关联图片记录失败');
                }

            }

            return $info->id;//已存在返回
        }

        $insArr = [
            'path'         => $path,
            'title'        => $title,
            'url'          => asset ($path),
            'md5'          => $md5,
            'sha1'         => $sha1,
            'picture_id'   => $picture_id,
            'picture_type' => $picture_type,
            'status'       => $status
        ];
        $info   = $this->create ($insArr);

        if (isset($info->id)) {
            return $info->id;//新增
        } else {
            throw new \ErrorException('新增图片记录失败');
        }
    }

    /**
     * 删除图片 add by gui
     * @param $pictureID
     * @return bool
     * @throws \ErrorException
     */
    public function deletePicture ($pictureID)
    {
        $info = $this->find ($pictureID);
        if (file_exists ($info->path))
            unlink ($info->path);
        $ret = $info->delete ();
        if ($ret) {
            return true;
        } else {
            throw new \ErrorException('删除图片失败');
        }

    }

    public function picture ()
    {
        return $this->morphTo ();
    }
}
