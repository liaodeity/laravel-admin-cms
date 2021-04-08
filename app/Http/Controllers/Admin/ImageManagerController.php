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
use App\Models\Log;
use App\Repositories\FeedbackRepository;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

class ImageManagerController extends Controller
{
    protected $module_name = 'image_manager';
    /**
     */
    private $repository;

    public function __construct ()
    {
        View::share ('MODULE_NAME', $this->module_name);//模块名称

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $r_path = $request->input ('path');
        $files  = Storage::disk ('web_public')->allFiles ('images');
        $images = [];
        foreach ($files as $file) {
            if ($r_path) {
                //存在搜索
                if (!stristr ($file, $r_path) && !stristr (asset ($file), $r_path)) {
                    continue;
                }
            }
            $images[] = $this->getImage ($file);
        }
        $images = (object)$images;

        return view ('admin.image_manager.index', compact ('images'));

    }

    protected function getImage ($file)
    {
        $size   = Storage::disk ('web_public')->size ($file);
        $time   = Storage::disk ('web_public')->lastModified ($file);
        $img    = Image::make ($file);
        $width  = $img->getWidth ();
        $height = $img->getHeight ();
        $image  = (object)[
            'path'         => $file,
            'url'          => asset ($file) . '?t=' . time (),
            'size'         => format_size ($size),
            'time'         => Carbon::parse (date ('Y-m-d H:i:s', $time))->toDateTimeString (),
            'width_height' => $width . '*' . $height,
        ];

        return $image;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create (Request $request)
    {
        $path    = $request->input ('path');
        $image   = $this->getImage ($path);
        $en_path = encrypt ($path);
        $en_path = base64_encode ($en_path);

        return view ('admin.image_manager.create', compact ('image', 'en_path'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show ($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit ($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy ($id)
    {
        //
    }

    /**
     * 上传图片 add by gui
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function upload (Request $request)
    {
        set_time_limit (0);
        $en_path = $request->input ('en_path');
        $en_path = base64_decode ($en_path);
        $path    = decrypt ($en_path);
        $exists  = Storage::disk ('web_public')->exists ($path);
        if (!$exists) {
            return ajax_error_result ('图片不存在，无法进行替换');
        }

        $name       = $request->input ('name', 'file');
        $images     = $request->file ($name);
        $imagesName = $images->getClientOriginalName ();
        $extension  = $images->getClientOriginalExtension ();
        $size       = $images->getSize ();
        $extension  = strtolower ($extension);
        if (!in_array ($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
            return ajax_error_result ('.' . $extension . '的后缀不允许上传');
        }
        $real_path = $images->getRealPath ();
        $content   = file_get_contents ($real_path);
        //备份图片
        $t_dir = now ()->format ('YmdHis');
        $ret   = Storage::disk ('web_public')->copy ($path, 'bak/' . $t_dir . '/' . $path);
        if ($ret) {
            $ret = Storage::disk ('web_public')->put ($path, $content);
        }
        if ($ret) {
            Log::createLog (Log::EDIT_TYPE, '图片替换管理替换图片[' . $path . ']记录', '');

            return ajax_success_result ('替换图片成功');
        } else {
            return ajax_error_result ('替换图片失败');
        }
    }
}
