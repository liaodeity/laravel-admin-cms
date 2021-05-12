@extends('layouts.app')
@section('style')

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
                                <label class="layui-form-label">所属角色</label>
                                <div class="layui-input-inline">
                                    <select name="role_id">
                                        <option value="">请选择</option>
                                        @foreach($roles as $ind => $item)
                                            <option value="{{$item->id}}">{{$item->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">用户名</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="username" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">真实姓名</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="realname" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-inline">
                                    <select name="status">
                                        <option value="">请选择</option>
                                        @foreach($user->statusItem() as $ind => $item)
                                            <option value="{{$ind}}">{{$item}}</option>
                                        @endforeach
                                    </select>
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
            <table class="layui-hide" id="currentTableId" lay-filter="currentTableFilter"></table>

            <script type="text/html" id="toolbarFilter">
                <div class="layui-btn-container">
                    @if( check_admin_auth ($MODULE_NAME.' create'))
                    <button class="layui-btn layui-btn-sm data-add-btn" lay-event="add"> 添加</button>
                    @endif
                </div>
            </script>
            <script type="text/html" id="currentTableBar">
                @if( check_admin_auth ($MODULE_NAME.' edit'))
                <a class="layui-btn layui-btn-xs data-count-edit" lay-event="edit">编辑</a>
                @endif
            </script>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        layui.use(['form', 'table'], function () {
            var $ = layui.jquery,
                form = layui.form,
                table = layui.table,
                layuimini = layui.layuimini;
            form.render();
            table.render({
                elem: '#currentTableId',
                url: '/admin/' + MODULE_NAME,
                toolbar: '#toolbarFilter',
                defaultToolbar: ['filter', ],
                cols: [[
                    {field: 'role', title: '所属角色', hide: false,sort:true},
                    {field: 'username', title: '登录账号',sort:true},
                    {field: 'user_no', title: '话务账号',sort:true},
                    {field: 'realname', title: '真实姓名',sort:true},
                    {field: 'telephone', title: '电话',hide:true,sort:true},
                    {field: 'email', title: '电子邮件',hide:true,sort:true},
                    {field: 'address', title: '地址', hide: true,sort:true},
                    {field: 'sex', title: '性别',width:100,sort:true},
                    {field: 'login_count', title: '登录次数',width:120,sort:true},
                    {field: 'created_at', title: '注册时间',width:180, hide: true,sort:true},
                    {field: 'updated_at', title: '更新时间',width:180,hide:true,sort:true},
                    {field: 'last_login_at', title: '最后登录时间',width:180,sort:true},
                    {field: 'status', title: '状态',width:100,sort:true},
                    {title: '操作', width: 220, templet: '#currentTableBar', fixed: "right", align: "center"}
                ]],
                limits: [10, 15, 20, 25, 50, 100],
                limit: 15,
                page: true,
            });

            // 监听搜索操作
            form.on('submit(data-search-btn)', function (data) {
                var result = JSON.stringify(data.field);
                // layer.alert(result, {
                //     title: '最终的搜索信息'
                // });

                //执行搜索重载
                table.reload('currentTableId', {
                    page: {
                        curr: 1
                    }
                    , where: {
                        searchParams: result
                    }
                }, 'data');

                return false;
            });
            //监听表格复选框选择
            table.on('checkbox(currentTableFilter)', function (obj) {
                console.log(obj)
            });

            table.on('toolbar(currentTableFilter)', function (obj) {
                console.log(obj);
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
                    case 'batch-delete':
                        var checkStatus = table.checkStatus('currentTableId')
                            , data = checkStatus.data;
                        layer.confirm('确认删除记录？', function (index) {
                            layer.msg('删除' + data.length + '条记录', {icon: 6});
                            layer.close(index);
                        });
                        break;
                    case 'link':
                        setTimeout(function () {
                            table.resize();//
                        }, TABLE_RESIZE_TIME)
                        break;
                    case 'LAYTABLE_EXPORT_DATA':
                        window.location.href = '/admin/' + MODULE_NAME + '/export?searchParams=' + searchParams;
                        break;
                    case 'LAYTABLE_TIPS':
                        top.layer_module_tips(MODULE_NAME)
                        break;
                }
            });
            table.on('tool(currentTableFilter)', function (obj) {
                var data = obj.data;
                console.log(data);
                if (obj.event === 'edit') {

                    var index = layer.open({
                        title: '',
                        type: 2,
                        shade: 0.2,
                        maxmin: false,
                        shadeClose: false,
                        area: ['60%', '65%'],
                        content: '/admin/' + MODULE_NAME + '/' + data.id + '/edit',
                    });
                    return false;
                } else if (obj.event === 'add') {
                    var index = layer.open({
                        title: '',
                        type: 2,
                        shade: 0.2,
                        maxmin: false,
                        shadeClose: false,
                        area: ['60%', '65%'],
                        content: '/admin/' + MODULE_NAME + '/create?pid=' + data.id,
                    });
                } else if (obj.event === 'view') {

                    var index = layer.open({
                        title: '',
                        type: 2,
                        shade: 0.2,
                        maxmin: false,
                        shadeClose: false,
                        area: ['60%', '65%'],
                        content: '/admin/' + MODULE_NAME + '/' + data.id,
                    });
                    return false;
                } else if (obj.event === 'delete') {
                    layer.confirm('确认删除记录？', function (index) {
                        obj.del();
                        layer.msg('删除1条记录', {icon: 6});
                        layer.close(index);
                    });
                } else {
                    console.log(obj.event);
                }
            });

            //监听排序事件
            table.on('sort(currentTableFilter)', function (obj) { //注：sort 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
                // console.log(obj.field); //当前排序的字段名
                // console.log(obj.type); //当前排序类型：desc（降序）、asc（升序）、null（空对象，默认排序）
                // console.log(this); //当前排序的 th 对象

                //尽管我们的 table 自带排序功能，但并没有请求服务端。
                //有些时候，你可能需要根据当前排序的字段，重新向服务端发送请求，从而实现服务端排序，如：
                table.reload('currentTableId', {
                    initSort: obj //记录初始排序，如果不设的话，将无法标记表头的排序状态。
                    , where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                        field: obj.field //排序字段
                        , order: obj.type //排序方式
                    }
                });

                // layer.msg('服务端排序。order by '+ obj.field + ' ' + obj.type);
            });
        });
    </script>
@endsection
