@extends('layouts.app')
@section('body_class','bg-write')
@section('style')

@endsection

@section('content')
    <div class="layui-container">
        <div class="layui-row">
            <table class="layui-table zhihe-show">
                <colgroup>
                    <col width="20%">
                    <col width="">
                    <col>
                </colgroup>
                <thead>
                </thead>
                <tbody>
                <tr>
                    <th>类型</th>
                    <td>{{$log->typeItem($log->type ?? '')}}</td>
                </tr>
                <tr>
                    <th>日志标题</th>
                    <td>{{$log->title ?? ''}}</td>
                </tr>
                @if(\App\Models\User::isSuperAdmin ())
                <tr>
                    <th>日志内容</th>
                    <td>{{$log->content ?? ''}}</td>
                </tr>
                @endif
                @if($backup_content)
                <tr>
                    <th>备份内容</th>
                    <td>{{$backup_content ?? ''}}</td>
                </tr>
                @endif

                <tr>
                    <th>创建时间</th>
                    <td>{{$log->created_at ?? ''}}</td>
                </tr>
                <tr>
                    <th>更新时间</th>
                    <td>{{$log->updated_at ?? ''}}</td>
                </tr>
                <tr>
                    <th>记录人</th>
                    <td>{{\App\Models\User::showName ($log->user_id ?? '')}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('footer')

@endsection
