@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layuimini-container">
        <div class="layuimini-main">
            <form class="layui-form" action="" lay-filter="example" onsubmit="return false;">
                <div class="layui-form layuimini-form">
                    {{csrf_field ()}}
                    <input type="hidden" name="id" value="{{$user->id ?? ''}}">
                    <div class="layui-form-item">
                        <label class="layui-form-label required">用户账号</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" lay-verify="required" disabled lay-reqtext="管理账号不能为空"
                                   placeholder="请输入管理账号" value="{{$user->name}}" class="layui-input layui-disabled">
                            <tip>登录账号无法修改。</tip>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">邮箱</label>
                        <div class="layui-input-block">
                            <input type="email" name="User[email]" placeholder="请输入邮箱" value="" class="layui-input">
                        </div>
                    </div>
                    @include('admin.user.form_user_info_add')
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="" lay-filter="create">立即提交</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'layarea', 'table', 'tableSelect'], function () {
            var form = layui.form
                , layer = layui.layer
                , layedit = layui.layedit
                , laydate = layui.laydate,
                layarea = layui.layarea;
            table = layui.layarea;
            form.render();
            //日期
            laydate.render({
                elem: '#date',
                trigger: 'click',
                range: true
            });

            //监听提交
            form.on('submit(create)', function (data) {
                $.ajax({
                    type: 'POST',
                    url: '{{url('admin/user/setting')}}',
                    data: data.field,
                    dataType: 'json',
                    beforeSend: function () {
                        $("#button[lay-filter='create']").removeClass('disabled').prop('disabled', false);
                        loading = layer.load(2)
                    },
                    complete: function () {
                        $("#button[lay-filter='create']").removeClass('disabled').prop('disabled', false);
                        layer.close(loading)
                    },
                    error: function () {
                        layer.msg(AJAX_ERROR_TIP, {
                            icon: 2,
                            time: FAIL_TIME,
                            shade: 0.3
                        });
                    },
                    success: function (data) {
                        if (data.code === 0) {
                            layer.msg(data.message, {icon: 6, time: SUCCESS_TIME, shade: 0.2});
                            setTimeout(function () {
                                location.reload();
                            }, SUCCESS_TIME)
                        } else {
                            layer.msg(data.message, {
                                icon: 2,
                                time: FAIL_TIME,
                                shade: 0.3
                            });
                        }

                    }
                })

                return false;
            });
            form.on('submit(jobs)', function (data) {
                var index = layer.open({
                    title: '',
                    type: 2,
                    shade: 0.2,
                    maxmin: false,
                    shadeClose: false,
                    area: ['100%', '100%'],
                    content: '/admin/jobs?source=select',
                });

                return false;
            });

            //开始使用
            var tableSelect = layui.tableSelect;
            tableSelect.render({
                elem: '#select_category_id',	//定义输入框input对象 必填
                checkedKey: 'id', //表格的唯一建值，非常重要，影响到选中状态 必填
                searchKey: 'title',	//搜索输入框的name值 默认keyword
                searchPlaceholder: '分类名称搜索',	//搜索输入框的提示文字 默认关键词搜索
                height: '250',  //自定义高度
                width: '520',  //自定义宽度
                table: {	//定义表格参数，与LAYUI的TABLE模块一致，只是无需再定义表格elem
                    url: '/admin/category?status=1',
                    width: '520',  //自定义宽度
                    cols: [[
                        {type: 'radio'},
                        {field: 'id', width: 80, title: 'ID'},
                        {field: 'title', title: '分类名称'},
                    ]]
                },
                done: function (elem, data) {
                    //选择完后的回调，包含2个返回值 elem:返回之前input对象；data:表格返回的选中的数据 []
                    //拿到data[]后 就按照业务需求做想做的事情啦~比如加个隐藏域放ID...
                    var NEWJSON = []
                    var IDJSON = []
                    // console.log(data);
                    layui.each(data.data, function (index, item) {
                        NEWJSON.push(item.title)
                        IDJSON.push(item.id)
                    })
                    $("#category_id").val(IDJSON.join(","))
                    elem.val(NEWJSON.join(","))
                }
            });
            tableSelect.render({
                elem: '#select_page_id',	//定义输入框input对象 必填
                checkedKey: 'id', //表格的唯一建值，非常重要，影响到选中状态 必填
                searchKey: 'title',	//搜索输入框的name值 默认keyword
                searchPlaceholder: '页面名称搜索',	//搜索输入框的提示文字 默认关键词搜索
                height: '250',  //自定义高度
                width: '520',  //自定义宽度
                table: {	//定义表格参数，与LAYUI的TABLE模块一致，只是无需再定义表格elem
                    url: '/admin/page?status=1',
                    width: '520',  //自定义宽度
                    cols: [[
                        {type: 'radio'},
                        {field: 'id', width: 80, title: 'ID'},
                        {field: 'link_label', title: '链接名称'},
                        {field: 'title', title: '页面名称'},
                    ]]
                },
                done: function (elem, data) {
                    //选择完后的回调，包含2个返回值 elem:返回之前input对象；data:表格返回的选中的数据 []
                    //拿到data[]后 就按照业务需求做想做的事情啦~比如加个隐藏域放ID...
                    var NEWJSON = []
                    var IDJSON = []
                    // console.log(data);
                    layui.each(data.data, function (index, item) {
                        NEWJSON.push(item.title)
                        IDJSON.push(item.id)
                    })
                    $("#page_id").val(IDJSON.join(","))
                    elem.val(NEWJSON.join(","))
                }
            });

        });
    </script>
@endsection
