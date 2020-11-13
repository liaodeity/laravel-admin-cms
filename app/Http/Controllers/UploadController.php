<?php

namespace App\Http\Controllers;

use App\Entities\Picture;
use Illuminate\Http\Request;


/**
 * 前台公共基类
 * Class BaseAdmin
 * @package App\Http\Controllers
 */
class UploadController extends Controller
{
    public function __construct()
    {

    }

    /**
     * 上传图片 add by gui
     * @param Request $request
     * @param         $name
     * @return array|false|string
     */
    public function image(Request $request, $name)
    {
        set_time_limit(0);
        $images = $request->file($name);
        $width = $request->width;
        $height = $request->height;
        $filedir = "upload/img/" . date('Ymd') . '/';
        $imagesName = $images->getClientOriginalName();
        $extension = $images->getClientOriginalExtension();
        $size = $images->getSize();
        $extension = strtolower($extension);
        if (!in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
            return ['status' => 0, 'info' => '.' . $extension . '的后缀不允许上传'];
        }

        $newImagesName = uniqid() . "." . $extension;

        $images->move($filedir, $newImagesName);
        $data['path'] = $filedir . $newImagesName;
        $data['url'] = asset($data['path']);
        $data['md5'] = md5_file($data['path']);
        $data['sha1'] = sha1_file($data['path']);
        $data['status'] = 1;
        $data['title'] = $imagesName;
        $pictrue = Picture::create($data);
        $data['id'] = $pictrue->id;
        $data['size'] = $size;
        $data['state'] = 'SUCCESS';
        $data['name'] = $newImagesName;
        $data['url'] = '/'.$data['path'];
        $data['type'] = '.'.$extension;
        $data['originalName'] = $data['title'];
        return json_encode($data);
    }

    /**
     * 上传附件 add by gui
     * @param Request $request
     * @param         $name
     * @return array
     */
    public function annex(Request $request, $name)
    {
        set_time_limit(0);
        $images = $request->file($name);
        $filedir = "upload/annex/" . date('Ymd') . '/';
        $imagesName = $images->getClientOriginalName();
        $extension = $images->getClientOriginalExtension();
        $extension = strtolower($extension);
        if (!in_array($extension, ['jpeg', 'jpg', 'png', 'gif', 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'mp4'])) {
            return ['status' => 0, 'info' => '.' . $extension . '的后缀不允许上传'];
        }
        $newImagesName = uniqid() . "." . $extension;
        $images->move($filedir, $newImagesName);
        $data['work_id'] = $request->work_id;
        $data['path'] = $filedir . $newImagesName;
        $data['url'] = asset($data['path']);
        $pictrue = Picture::create($data);
        $data['id'] = $pictrue->id;
        $data['title'] = $imagesName;
        $data['state'] = 'SUCCESS';
        $data['originalName'] = $data['title'];

        return $data;
    }
}
