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

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * 上传图片 add by gui
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function image (Request $request)
    {
        set_time_limit (0);
        $sourceType = $request->input ('type');
        $sourceId   = $request->input ('id', '');
        if ($sourceType) {
            $sourceType = urldecode ($sourceType);
        }
        $name       = $request->input ('name', 'upfile');
        $images     = $request->file ($name);
        $filedir    = "uploads/" . date ('Ym') . '/';
        $imagesName = $images->getClientOriginalName ();
        $extension  = $images->getClientOriginalExtension ();
        $size       = $images->getSize ();
        $extension  = strtolower ($extension);
        if (!in_array ($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
            return ['status' => 0, 'info' => '.' . $extension . '的后缀不允许上传'];
        }

        $newImagesName = get_uuid () . "." . $extension;


        $path    = $filedir . $newImagesName;
        $content = file_get_contents ($images->getRealPath ());
        Storage::disk ('public')->put ($path, $content);
        $public_path = 'storage/' . $path;
        $insArr      = [
            'name'        => $imagesName,
            'path'        => $public_path,
            'file_md5'    => md5_file ($public_path),
            'file_sha1'   => sha1_file ($public_path),
            'source_type' => $sourceType,
            'source_id'   => $sourceId,
            'status'      => 1
        ];
        $Attachment  = Attachment::addFile ($insArr);
        if (!$Attachment) {
            return ajax_error_result ('上传失败');
        }
        $data['id']           = $Attachment->id;
        $data['size']         = $size;
        $data['state']        = 'SUCCESS';
        $data['name']         = $newImagesName;
        $data['url']          = '/' . $Attachment->path;
        $data['type']         = '.' . $extension;
        $data['originalName'] = $Attachment->name;

        // 图片水印
        //$watermark = get_config_value ('watermark_text', '');
        //if ($watermark) {
        //    $img = \Intervention\Image\Facades\Image::make ($path);
        //    $img->text ($watermark, $img->width () - 10, $img->height () - 10, function ($font) {
        //        $font_dir = public_path ('fonts/gdht.ttf');
        //        $font->file ($font_dir);
        //        $font->size (18);
        //        $font->color ('#FFFFFF');
        //        $font->align ('right');
        //        $font->valign ('bottom');
        //    });
        //    $img->save ($path);
        //}

        $data['src']  = $data['url'];
        $data['code'] = 0;
        Log::createLog (Log::INFO_TYPE, '上传图片记录', '', $Attachment->id, Attachment::class);

        return json_encode ($data);
    }

    /**
     * 上传表格 add by gui
     * @param Request $request
     * @param string  $name
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function excel (Request $request, $name = 'file')
    {
        set_time_limit (0);
        $files      = $request->file ($name);
        $filedir    = "Uploads/excel/" . date ('Ymd') . '/';
        $imagesName = $files->getClientOriginalName ();
        $extension  = $files->getClientOriginalExtension ();
        $size       = $files->getSize ();
        $extension  = strtolower ($extension);
        if (!in_array ($extension, ['xls', 'xlsx'])) {
            return ['status' => 0, 'info' => '.' . $extension . '的后缀不允许上传'];
        }

        $newImagesName = get_uuid () . "." . $extension;

        $files->move ($filedir, $newImagesName);
        $path       = $filedir . $newImagesName;
        $insArr     = [
            'name'      => $imagesName,
            'path'      => $path,
            'file_md5'  => md5_file ($path),
            'file_sha1' => sha1_file ($path),
            'status'    => 1
        ];
        $attachment = Attachment::addFile ($insArr);

        $result = [
            'data' => [
                'id'    => $attachment->id,
                'name'  => $attachment->name,
                'title' => str_replace ('.' . $extension, '', $attachment->name),
                'src'   => asset ($attachment->path)
            ]
        ];
        Log::createLog (Log::INFO_TYPE, '上传附件记录', '', $attachment->id, Attachment::class);

        return ajax_success_result ('上传成功', $result);
    }
}
