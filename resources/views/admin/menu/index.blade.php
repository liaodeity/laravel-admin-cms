@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="main-inner layui-fluid">
        <div class="main-layout main-order">
            <fieldset class="layui-elem-field">
                <legend>{{__('message.lists.search_info')}}</legend>
                <div style="margin: 10px 10px 10px 10px">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">编号</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="menus[id]" autocomplete="off" class="layui-input">
                                </div>
                            </div><div class="layui-inline">
                                <label class="layui-form-label">父ID</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="menus[pid]" autocomplete="off" class="layui-input">
                                </div>
                            </div><div class="layui-inline">
                                <label class="layui-form-label">菜单标识名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="menus[menu_name]" autocomplete="off" class="layui-input">
                                </div>
                            </div><div class="layui-inline">
                                <label class="layui-form-label">权限名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="menus[auth_name]" autocomplete="off" class="layui-input">
                                </div>
                            </div><div class="layui-inline">
                        <label class="layui-form-label">所属模块</label>
                        <div class="layui-input-inline">
                            <select name="menus[module]" lay-filter="module">
                                <option value=""></option>
                                @foreach($menu->moduleItem() as $ind=>$val)
                                <option value="{{$ind}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div><div class="layui-inline">
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-inline">
                            <select name="menus[type]" lay-filter="type">
                                <option value=""></option>
                                @foreach($menu->typeItem() as $ind=>$val)
                                <option value="{{$ind}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div><div class="layui-inline">
                                <label class="layui-form-label">路由地址</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="menus[route_url]" autocomplete="off" class="layui-input">
                                </div>
                            </div><div class="layui-inline">
                                <label class="layui-form-label">菜单名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="menus[title]" autocomplete="off" class="layui-input">
                                </div>
                            </div><div class="layui-inline">
                                <label class="layui-form-label">图标</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="menus[icon]" autocomplete="off" class="layui-input">
                                </div>
                            </div><div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="menus[status]" lay-filter="status">
                                <option value=""></option>
                                @foreach($menu->statusItem() as $ind=>$val)
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
        @{{# if (d._show_url) { }}
        <a class="layui-btn layui-btn-xs data-count-show" href="@{{d._show_url}}" lay-event="show">{{__('message.buttons.show')}}</a>
        @{{# } }}
        @{{# if (d._edit_url) { }}
        <a class="layui-btn layui-btn-xs data-count-edit" href="@{{d._edit_url}}" lay-event="edit">{{__('message.buttons.edit')}}</a>
        @{{# } }}
        @{{# if (d._delete_url) { }}
        <a class="layui-btn layui-btn-xs layui-btn-danger data-count-delete" lay-event="delete">{{__('message.buttons.delete')}}</a>
        @{{# } }}
    </script>
@endsection

@section('footer')

    <script>
        layui.use ([ 'table', 'treetable','miniAdmin' ], function () {
            var $ = layui.jquery;
            var table = layui.table;
            var treetable = layui.treetable;
            var miniAdmin = layui.miniAdmin;

            // 渲染表格
            layer.load (2);

            treetable.render ({
                treeColIndex: 1,
                treeSpid: 0,
                treeIdName: 'id',
                treePidName: 'pid',
                elem: '#currentTableId',
                toolbar: '#toolbarFilter',
                parseData: function(res){
                    return {
                        "code": res.code,
                        "msg": res.message,
                        "count": res.result.count,
                        "data": res.result.data
                    };
                },
                defaultToolbar: ['filter', ],
                url: '{{url('admin/menu')}}',
                page: false,
                where: {
                    title: '{{request ()->input ('title')}}',
                    status: '{{request ()->input ('status')}}'
                },
                cols: [ [
                    {type: 'numbers'},
                    {field: 'title', minWidth: 200, title: '权限名称'},
                    {field: 'auth_name', title: '权限标识'},
                    {field: 'route_url', title: '菜单url'},
                    {field: 'sort', width: 80, align: 'center', title: '排序号'},
                    {
                        field: 'isMenu', width: 80, align: 'center', templet: function (d) {
                            if (d.type == 2) {
                                return '<span class="layui-badge layui-btn-warm">按钮</span>';
                            }
                            if (d.pid == 0) {
                                return '<span class="layui-badge layui-bg-blue">目录</span>';
                            } else {
                                return '<span class="layui-badge-rim">菜单</span>';
                            }
                        }, title: '类型'
                    },
                    {templet: '#operateTableBar', width: 120, align: 'center', title: '操作'}
                ] ],
                done: function () {
                    layer.closeAll ('loading');
                }
            });

            $ ('#btn-expand').click (function () {
                treetable.expandAll ('#currentTableFilter');
            });

            $ ('#btn-fold').click (function () {
                treetable.foldAll ('#currentTableFilter');
            });

            //监听工具条
            table.on ('tool(currentTableFilter)', function (obj) {
                var data = obj.data;
                var layEvent = obj.event;
                console.log(layEvent);
                if (layEvent === 'del') {
                    layer.msg ('删除' + data.id);
                } else if (layEvent === 'edit') {
                    systemGui.getHrefContentOpen(SYSTEM_GUI.ROUTE_PREFIX+ '/'+ data.id + '/edit');
                }
            });
        });
    </script>
@endsection
