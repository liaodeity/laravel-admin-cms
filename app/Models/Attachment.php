<?php
/*
|-----------------------------------------------------------------------------------------------------------
| laravel-admin-cms [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( MIT )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2020-2021 https://gitee.com/liaodeiy/laravel-admin-cms All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/

namespace App\Models;

use App\Exceptions\BusinessException;
use App\Services\FileSystem\UploadService;
use App\Traits\DateTimeFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use DateTimeFormat;
    protected $fillable = ['user_id', 'name', 'path', 'storage_path', 'url', 'file_md5', 'file_sha1', 'file_size', 'driver', 'status', 'source_type', 'source_id'];

    /**
     * 保存附件信息，根据SHA和MD5判断是否重复，重复标记记录status=-1，
     * 由定时任务清理重复附件，释放空间
     * add by gui
     * @param $insArr
     * @return mixed
     */
    public static function addFile ($insArr)
    {
        if (array_get ($insArr, 'user_id', 0) == 0) {
            $insArr['user_id'] = get_login_user_id ();
        }
        $driver = config ('gui.upload_driver');
        $md5    = array_get ($insArr, 'file_md5', '');
        $sha1   = array_get ($insArr, 'file_sha1', '');
        $pic    = Attachment::where ('file_md5', $md5)->where ('file_sha1', $sha1)->first ();
        if (isset($pic->id) && file_exists ($pic->path)) {
            //相同文件存在
            if (empty($pic->file_size)) {
                $size           = filesize ($insArr['path']);
                $pic->file_size = $size > 0 ? $size / 1024 : 0;
                $pic->save ();
            }
            if (empty($pic->driver) && $driver && $pic->storage_path) {
                //云上传
                $url = UploadService::disk ()->upload ($insArr['storage_path']);
                if ($url) {
                    $pic->driver = $driver;
                    $pic->url    = $url;
                    $pic->save ();
                }
            }

            return $pic;
        }
        $size                = filesize ($insArr['path']);
        $insArr['file_size'] = $size > 0 ? $size / 1024 : 0;
        $insArr['url']       = '/' . $insArr['path'] ?? '';
        if (isset($insArr['storage_path']) && $driver && $insArr['storage_path']) {
            //云上传
            $url = UploadService::disk ()->upload ($insArr['storage_path']);
            if ($url) {
                $insArr['driver'] = $driver;
                $insArr['url']    = $url;
            }
        }

        return Attachment::create ($insArr);
    }

    /**
     * 删除附件，同时上传云文件和本地文件 add by gui
     * @param Attachment $attachment
     * @return bool
     * @throws BusinessException
     */
    public static function deleteFile (Attachment $attachment)
    {
        if ($attachment->driver) {
            //刪除云文件
            $ret = UploadService::disk ($attachment->driver)->delete ($attachment->storage_path);
            if (!$ret) {
                throw new BusinessException('远程云文件删除失败');
            }
        }
        if (Storage::disk ('public')->exists ($attachment->storage_path)) {
            $ret = Storage::disk ('public')->delete ($attachment->storage_path);
            if (!$ret) {
                throw new BusinessException('本地文件删除失败');
            }
        }
        if ($attachment->delete ()) {
            return true;
        } else {
            return false;
        }

    }
}
