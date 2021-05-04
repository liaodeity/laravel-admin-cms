@extends('layouts.app')
@section('style')
 <style>
     .layui-table img{
         max-width: 200px;
     }
 </style>
@endsection

@section('content')
    <div class="layuimini-container">
        <div class="layuimini-main">

            <fieldset class="table-search-fieldset">
                <legend>搜索信息</legend>
                <div style="margin: 10px 10px 10px 10px">
                    <form class="layui-form layui-form-pane" lay-filter="data-search-filter" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">图片名称</label>
                                <div class="layui-input-inline" style="width: 420px;">
                                    <input type="text" name="path" autocomplete="off" value="{{request ()->input ('path')}}" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <button type="submit" class="layui-btn layui-btn-primary" lay-submit lay-filter="data-search-btn"><i
                                        class="layui-icon"></i> 搜 索
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </fieldset>
            <table class="layui-table">
                <colgroup>
                    <col width="">
                    <col width="">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>图片名称</th>
                    <th>尺寸（长度px*宽度px）</th>
                    <th>大小</th>
                    <th>预览</th>
                    <th>最后修改时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($images as $image)
                    <tr>
                        <td>{{$image->path}}</td>
                        <td>{{$image->width_height}}</td>
                        <td>{{$image->size}}</td>
                        <td><img lay-src="{{$image->url}}" alt=""></td>
                        <td>{{$image->time}}</td>
                        <td><a class="layui-btn layui-btn-xs upload-image" data-path="{{$image->path}}">替换</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(function () {
            $(".upload-image").click(function () {
                path = $(this).data('path')
                var index = top.layer.open({
                    title: '上传替换图片',
                    type: 2,
                    shade: 0.2,
                    maxmin: false,
                    shadeClose: false,
                    area: ['100%', '100%'],
                    content: '/admin/' + MODULE_NAME + '/create?path='+path,
                });
                top.layer.full(index)
            });
        })
        layui.use(['flow'], function () {
            var flow = layui.flow;

            flow.lazyimg();
        });
    </script>
@endsection
