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
                                <label class="layui-form-label">上传日期</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="created_at" id="created_at" readonly placeholder="选择日期范围" autocomplete="off"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">附件名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="name" autocomplete="off" class="layui-input">
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
                    <button class="layui-btn layui-btn-sm data-add-btn" id="file_upload"> <i class="layui-icon"></i>上传附件</button>
                </div>
            </script>
            <script type="text/html" id="currentTableBar">
                @if( check_admin_auth ($MODULE_NAME.' show'))
                @{{# if(d._src){ }}
                <a target="_blank" href="@{{ d._src }}" class="layui-btn layui-btn-xs layui-btn-primary">预览</a>
                @{{# } }}
                @endif
                @if( check_admin_auth ($MODULE_NAME.'_delete'))
                <a class="layui-btn layui-btn-xs layui-btn-danger data-count-delete" lay-event="delete">{{__('message.buttons.delete')}}</a>
                @endif
            </script>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        layui.use(['form', 'table', 'laydate', 'upload'], function () {
            var $ = layui.jquery,
                form = layui.form,
                table = layui.table,
                layuimini = layui.layuimini;
            upload = layui.upload;
            laydate = layui.laydate;
            form.render();

            table.render({
                elem: '#currentTableId',
                url: '/admin/' + MODULE_NAME,
                toolbar: '#toolbarFilter',
                defaultToolbar: ['filter'],
                where: {
                    type: '{{request ()->input ('type')}}',
                    source_id: '{{request ()->input('source_id')}}',
                    source_type: '{{request ()->input('source_type')}}',
                },
                cols: [[
                    {field: 'name', title: '附件名称', sort: true},
                    {field: '_src', title: '附件地址',},
                    {field: '_w_h', title: '尺寸（长度px*宽度px）', width: 180,},
                    {field: '_size', title: '大小', width: 100,},
                    {field: 'file_md5', title: 'MD5', hide: true,},
                    {field: 'file_sha1', title: 'SHA1', hide: true},
                    {field: 'user_id', title: '记录人', width: 180, sort: true},
                    {field: 'status', title: '状态', width: 100, sort: true},
                    {field: 'created_at', title: '上传时间', width: 180, sort: true},
                    {title: '操作', width: 120, templet: '#currentTableBar', fixed: "right", align: "center"}
                ]],
                limits: [10, 15, 20, 25, 50, 100],
                limit: 15,
                page: true,
                done:function (res) {
                    upload.render({
                        elem: '#file_upload' //绑定元素
                        , url: '{!! get_upload_url(route('upload.file'), $attachment) !!}' //上传接口
                        , accept: 'file'
                        , done: function (res) {
                            //code=0代表上传成功
                            if (res.code === 0) {
                                top.layer.msg(res.message, {
                                    icon: 1,
                                    time: SUCCESS_TIME,
                                    shade: 0.3
                                });
                                setTimeout(function () {
                                    $(".layui-form button[type='submit']").click();
                                }, SUCCESS_TIME)

                            } else {
                                top.layer.msg(res.message, {
                                    icon: 2,
                                    time: FAIL_TIME,
                                    shade: 0.3
                                });
                            }
                        }
                        , error: function () {
                            //请求异常回调
                            top.layer.msg('上传接口异常', {
                                icon: 2,
                                time: FAIL_TIME,
                                shade: 0.3
                            });
                        }
                    });
                }
            });
            //日期
            laydate.render({
                elem: '#created_at',
                trigger: 'click'
                , range: true
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
                    case 'export_data':
                        window.location.href = '/admin/' + MODULE_NAME + '/export?searchParams=' + searchParams;
                        break;
                    case 'LAYTABLE_TIPS':
                        top.layer_module_tips(MODULE_NAME)
                        break;
                }
            });
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
                        layer.msg('确认永久删除附件？', {
                            time: 0 //不自动关闭
                            ,btn: ['确认', '取消']
                            ,yes: function(index){
                                layer.close(index);
                                $.ajax({
                                    type: 'POST',
                                    url: '/admin/' + MODULE_NAME + '/' + data.id,
                                    data: {
                                        _method: 'DELETE'
                                    },
                                    dataType: 'json',
                                    beforeSend: function () {
                                        loading = layer.load(2)
                                    },
                                    complete: function () {
                                        layer.close(loading)
                                    },
                                    error: function () {
                                        top.layer.msg(AJAX_ERROR_TIP, {
                                            icon: 2,
                                            time: FAIL_TIME,
                                            shade: 0.3
                                        });
                                    },
                                    success: function (data) {
                                        if (data.code === 0) {
                                            layer.msg(data.message, {icon: 6, time: SUCCESS_TIME, shade: 0.2});
                                            setTimeout(function () {
                                                $('button[lay-filter="data-search-btn"]').click();//刷新列表
                                            }, SUCCESS_TIME);
                                        } else {
                                            top.layer.msg(data.message, {
                                                icon: 2,
                                                time: FAIL_TIME,
                                                shade: 0.3
                                            });
                                        }
                                    }
                                });
                            }
                        });
                        break;
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
