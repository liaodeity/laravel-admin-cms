@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layuimini-container">
        <div class="layuimini-main">
            <fieldset class="layui-elem-field">
                <legend>{{__('message.lists.search_info')}}</legend>
                <div style="margin: 10px 10px 10px 10px">
                    <form class="layui-form layui-form-pane" lay-filter="data-search-filter" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">菜单名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">权限名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="auth_name" autocomplete="off" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-inline">
                                <label class="layui-form-label">路由地址</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="href" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-inline">
                                    <select name="status" lay-filter="status">
                                        <option value=""></option>
                                        @foreach(\App\Enums\MenuStatusEnum::attrs () as $ind=>$val)
                                            <option value="{{$ind}}">{{$val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <button type="submit" class="layui-btn layui-btn-primary" lay-submit
                                        lay-filter="data-search-btn"><i
                                        class="layui-icon"></i>{{__('message.buttons.search')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </fieldset>
            <div class="layui-tab order-content layuiwdl-tab-card">
                <div class="layui-tab-item layui-show">
                    <table class="layui-hide" id="currentTableId" lay-filter="currentTableFilter"></table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/html" id="toolbarFilter">
        <div class="layui-btn-container">
            @if( check_admin_auth ($MODULE_NAME.'_create'))
                <button class="layui-btn layui-btn-sm data-add-btn" lay-event="add">{{__ ('message.buttons.create')}}</button>
            @endif
        </div>
    </script>
    <script type="text/html" id="toolbarDemo">
        <div class="layui-btn-container">
            <a href="{{url('admin/menu/create')}}" class="layui-btn layui-btn-sm data-add-btn"
               lay-event="create"> {{__('message.buttons.create')}}
            </a>
            <button class="layui-btn layui-btn-sm layui-btn-danger data-delete-btn"
                    lay-event="delete"> {{__('message.buttons.delete')}}
            </button>
        </div>
    </script>
    <script type="text/html" id="operateTableBar">
        @{{# if (d._view_auth) { }}
        <a class="layui-btn layui-btn-xs data-count-show" lay-event="view">{{__('message.buttons.show')}}</a>
        @{{# } }}
        @{{# if (d._edit_url) { }}
        <a class="layui-btn layui-btn-xs data-count-edit" lay-event="edit">{{__('message.buttons.edit')}}</a>
        @{{# } }}
        @{{# if (d._delete_url) { }}
        <a class="layui-btn layui-btn-xs layui-btn-danger data-count-delete" lay-event="delete">{{__('message.buttons.delete')}}</a>
        @{{# } }}
    </script>
@endsection

@section('footer')

    <script>
        layui.use(['table','form', 'treetable', 'miniAdmin'], function () {
            var $ = layui.jquery;
            var table = layui.table;
            var form = layui.form;
            var treetable = layui.treetable;
            var miniAdmin = layui.miniAdmin;
            form.render();
            // 渲染表格
            layer.load(2);
           var treeConfig = {
                treeColIndex: 1,
                treeSpid: 0,
                treeIdName: 'id',
                treePidName: 'pid',
                elem: '#currentTableId',
                toolbar: '#toolbarFilter',
                parseData: function (res) {
                    return {
                        "code": res.code,
                        "msg": res.message,
                        "count": res.result.count,
                        "data": res.result.data
                    };
                },
                defaultToolbar: ['filter',],
                url: '{{url('admin/menu')}}',
                page: false,
                where: {
                    title: '{{request ()->input ('title')}}',
                    status: '{{request ()->input ('status')}}'
                },
                cols: [[
                    {type: 'numbers'},
                    {field: 'title', minWidth: 200, title: '菜单名称'},
                    {field: 'auth_name', title: '权限标识'},
                    {field: 'href', title: '路由地址'},
                    {field: 'sort', width: 80, align: 'center', title: '排序号'},
                    {field: 'status', width: 80, align: 'center', title: '状态'},
                    {templet: '#operateTableBar', width: 120, align: 'center', title: '操作'}
                ]],
                done: function () {
                    layer.closeAll('loading');
                }
            };
            treetable.render(treeConfig);
            // 监听搜索操作
            form.on('submit(data-search-btn)', function (data) {
                var result = JSON.stringify(data.field);
                // layer.alert(result, {
                //     title: '最终的搜索信息'
                // });
                // console.log(result);
                //执行搜索重载
                treeConfig.where = data.field;
                treeConfig.data = null;
                treeConfig.url = '{{url('admin/menu')}}';
                // console.log(treeConfig);
                layer.load(2);
                treetable.render(treeConfig)

                return false;
            });
            $('#btn-expand').click(function () {
                treetable.expandAll('#currentTableFilter');
            });

            $('#btn-fold').click(function () {
                treetable.foldAll('#currentTableFilter');
            });
            table.on('toolbar(currentTableFilter)', function (obj) {
                var data = form.val("data-search-filter");
                var searchParams = JSON.stringify(data);
                switch (obj.event) {
                    case 'add':
                        var index = layer.open({
                            title: '',
                            type: 2,
                            shade: 0.2,
                            maxmin: false,
                            shadeClose: false,
                            area: ['60%', '65%'],
                            content: '/admin/' + MODULE_NAME + '/create',
                        });
                        break;
                }
            });
            //监听工具条
            table.on('tool(currentTableFilter)', function (obj) {
                var data = obj.data;
                switch (obj.event) {
                    case 'edit':
                        var index = layer.open({
                            title: '',
                            type: 2,
                            shade: 0.2,
                            maxmin: false,
                            shadeClose: false,
                            area: ['60%', '65%'],
                            content: '/admin/' + MODULE_NAME + '/' + data.id + '/edit',
                        });
                        break;
                    case 'view':
                        var index = layer.open({
                            title: '',
                            type: 2,
                            shade: 0.2,
                            maxmin: false,
                            shadeClose: false,
                            area: ['60%', '65%'],
                            content: '/admin/' + MODULE_NAME + '/' + data.id,
                        });
                        break;
                    case 'delete':

                        break;
                }
            });
        });
    </script>
@endsection
