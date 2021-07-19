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
                    <th>配置分类</th>
                    <td>{{$config->group->title ?? ''}}</td>
                </tr>
                <tr>
                    <th>配置类型</th>
                    <td>{{\App\Enums\ConfigTypeEnum::toLabel ($config->type)}}</td>
                </tr>
                <tr>
                    <th>配置名称</th>
                    <td>{{$config->name ?? ''}}</td>
                </tr>
                <tr>
                    <th>配置标题</th>
                    <td>{{$config->title ?? ''}}</td>
                </tr>
                <tr>
                    <th>配置内容</th>
                    <td>{{$config->content ?? ''}}</td>
                </tr>
                <tr>
                    <th>说明</th>
                    <td>{{$config->desc ?? ''}}</td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td>{{$config->created_at ?? ''}}</td>
                </tr>
                <tr>
                    <th>更新时间</th>
                    <td>{{$config->updated_at ?? ''}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('footer')

@endsection
