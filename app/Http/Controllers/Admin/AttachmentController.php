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

use App\Enums\AttachmentStatusEnum;
use App\Enums\StatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Models\Attachment;
use App\Models\Log;
use App\Models\User;
use App\Repositories\AttachmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

class AttachmentController extends Controller
{
    protected $module_name = 'attachment';
    /**
     * @var AttachmentRepository
     */
    private $repository;

    public function __construct (AttachmentRepository $repository)
    {
        View::share ('MODULE_NAME', $this->module_name);//模块名称
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        if (!check_admin_auth ($this->module_name . '_' . __FUNCTION__)) {
            return auth_error_return ();
        }
        $category_id = $request->input ('category_id', 0);
        if (request ()->wantsJson ()) {
            $limit = $request->input ('limit', 15);
            QueryWhere::defaultOrderBy ('attachments.id', 'DESC')->setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('attachments.*');
            QueryWhere::date ($M, 'attachments.created_at');
            QueryWhere::like ($M, 'attachments.name');
            QueryWhere::orderBy ($M);

            $M     = $M->paginate ($limit);
            $count = $M->total ();
            $data  = $M->items ();
            foreach ($data as $key => $item) {
                $wh    = '-';
                $size  = '-';
                $src   = '';
                $path  = $item->storage_path;
                $exits = Storage::disk ('public')->exists ($path);
                if ($exits) {
                    $src      = $item->url ? $item->url : asset ($item->path);
                    $size     = Storage::disk ('public')->size ($path);
                    $size     = format_size ($size);
                    $mineType = mime_content_type ($item->path);
                    if ($mineType && strstr ($mineType, 'image')) {
                        $img    = Image::make ($item->path);
                        $width  = $img->getWidth ();
                        $height = $img->getHeight ();
                        $wh     = $width . '*' . $height;
                    }
                }

                $data[ $key ]['_src']    = $src;
                $data[ $key ]['_w_h']    = $wh;
                $data[ $key ]['_size']   = $size;
                $data[ $key ]['user_id'] = User::showName ($item->user_id);
                $data[ $key ]['status']  = AttachmentStatusEnum::toHtml ($item->status);
            }
            $result = [
                'count' => $count,
                'data'  => $data
            ];

            return ajax_success_result ('成功', $result);

        } else {
            $attachment = $this->repository->makeModel ();

            return view ('admin.' . $this->module_name . '.index', compact ('attachment', 'category_id'));
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     * @throws BusinessException
     */
    public function destroy ($id, Request $request)
    {
        if (!check_admin_auth ($this->module_name . '_delete')) {
            return auth_error_return ();
        }
        $ids = $request->input ('ids', []);
        if (empty($ids)) {
            $ids[] = $id;
        }
        DB::beginTransaction ();
        $ids   = (array)$ids;
        $M     = $this->repository->makeModel ();
        $lists = $M->whereIn ('id', $ids)->get ();
        $num   = 0;
        foreach ($lists as $item) {
            $log_title = '删除附件[' . ($item->category->title ?? '') . '->' . $item->title . ']记录';
            $check     = $this->repository->allowDelete ($item->id);
            if ($check) {
                $ret = Attachment::deleteFile ($item);
                if ($ret) {
                    Log::createLog (Log::DELETE_TYPE, $log_title, $item, $item->id, Attachment::class);
                    $num++;
                }
            }
        }
        DB::commit ();
        return ajax_success_result ('成功删除' . $num . '条记录');
    }
}
