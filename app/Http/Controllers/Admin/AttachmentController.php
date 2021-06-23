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
                    $src      = asset ($item->path);
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create (Request $request)
    {
        $attachment         = $this->repository->makeModel ();
        $_method            = 'POST';
        $attachment->status = StatusEnum::NORMAL;

        return view ('admin.' . $this->module_name . '.add', compact ('attachment', '_method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        $request->validate ([
            'Attachment.title'   => 'required',
            'Attachment.name'    => 'unique:attachments,name',
            'Attachment.content' => 'required',
            'Attachment.status'  => 'required',
        ], [], [
            'Attachment.title'   => '标题',
            'Attachment.name'    => '英文标识',
            'Attachment.content' => '内容',
            'Attachment.status'  => '状态',
        ]);
        if (!check_admin_auth ($this->module_name . '_edit')) {
            return auth_error_return ();
        }
        $input = $request->input ('Attachment');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $input['user_id'] = get_login_user_id ();
            $attachment       = $this->repository->create ($input);
            if ($attachment) {
                $log_title = '添加附件记录';
                Log::createLog (Log::ADD_TYPE, $log_title, '', $attachment->id, Attachment::class);

                return ajax_success_result ('添加成功');
            } else {
                return ajax_success_result ('添加失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
    }

    private function formatRequestInput (string $__FUNCTION__, $input)
    {
        return $input;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Attachment $attachment
     * @return \Illuminate\Http\Response
     */
    public function show (Attachment $attachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Attachment $attachment
     * @return \Illuminate\Http\Response
     */
    public function edit (Attachment $attachment)
    {
        $_method = 'PUT';

        return view ('admin.' . $this->module_name . '.add', compact ('attachment', '_method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attachment   $attachment
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, Attachment $attachment)
    {
        $request->validate ([
            'Attachment.title'   => 'required',
            'Attachment.name'    => 'unique:attachments,name,' . $attachment->id,
            'Attachment.content' => 'required',
            'Attachment.status'  => 'required',
        ], [], [
            'Attachment.title'   => '标题',
            'Attachment.name'    => '英文标识',
            'Attachment.content' => '内容',
            'Attachment.status'  => '状态',
        ]);
        $input = $request->input ('Attachment');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $input['user_id'] = get_login_user_id ();
            $attachment       = $this->repository->update ($input, $attachment->id);
            if ($attachment) {
                $content   = $attachment->toArray () ?? '';
                $log_title = '修改附件记录';
                Log::createLog (Log::EDIT_TYPE, $log_title, $content, $attachment->id, Attachment::class);

                return ajax_success_result ('修改成功');
            } else {
                return ajax_success_result ('修改失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
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
        $ids = $request->input ('ids', []);
        if (empty($ids)) {
            $ids[] = $id;
        }
        $ids   = (array)$ids;
        $M     = $this->repository->makeModel ();
        $lists = $M->whereIn ('id', $ids)->get ();
        $num   = 0;
        foreach ($lists as $item) {
            try {
                $this->repository->checkAuth ($item);
            } catch (BusinessException $e) {
                return ajax_error_result ($e->getMessage ());
            }
            $log_title = '删除附件[' . ($item->category->title ?? '') . '->' . $item->title . ']记录';
            $check     = $this->repository->allowDelete ($item->id);
            if ($check) {
                $ret = $this->repository->delete ($item->id);
                if ($ret) {
                    Log::createLog (Log::DELETE_TYPE, $log_title, $item, $item->id, Attachment::class);
                    $num++;
                }
            }
        }

        return ajax_success_result ('成功删除' . $num . '条记录');
    }
}
