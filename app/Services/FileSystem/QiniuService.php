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

namespace App\Services\FileSystem;


use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Storage;

class QiniuService
{

    /**
     *  add by gui
     * @param $path
     * @throws BusinessException
     */
    public function upload ($path)
    {
        //$path = str_replace ('storage/', '', $path);
        if (!Storage::disk ('public')->exists ($path)) {
            throw  new BusinessException('本地文件不存在' . $path);
        }
        $storagePath = Storage::disk ('public')->path ($path);
        $ret         = Storage::disk ('qiniu')->put ($path, $storagePath);
        if ($ret) {
            $url = Storage::disk ('qiniu')->url ($path);

            return $url;
        }
        throw  new BusinessException('云文件上传失败');
    }

    /**
     *  add by gui
     * @param $path
     * @return bool
     * @throws BusinessException
     */
    public function delete ($path)
    {
        //$path = str_replace ('storage/', '', $path);
        if (!Storage::disk ('qiniu')->exists ($path)) {
            //throw  new BusinessException('云文件不存在');
            return true;
        }
        $ret = Storage::disk ('qiniu')->delete ($path);

        return $ret;
    }
}
