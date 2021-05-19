@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layui-container">
        <div class="layui-row">
            <form class="layui-form" action="" lay-filter="example" onsubmit="return false;">
                {{ method_field($_method ?? '') }}
                {{csrf_field ()}}
                <input type="hidden" name="id" value="{{$user->id ?? ''}}">
                <div class="layui-form-item">
                    <label class="layui-form-label">登录账号 <span class="color-red">*</span></label>
                    <div class="layui-input-inline ">
                        <input type="text" class="layui-input " name="User[username]" maxlength="50" value="{{$user->username ?? ''}}" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">话务账号 <span class="color-red"></span></label>
                    <div class="layui-input-inline ">
                        <input type="text" class="layui-input " name="User[user_no]" maxlength="50" value="{{$user->user_no ?? ''}}" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">用户密码 <span class="color-red"></span></label>
                    <div class="layui-input-inline ">
                        <input type="password" class="layui-input " maxlength="64" name="User[password]" value="" >
                    </div>
                    <div class="layui-form-mid layui-word-aux ">如为空，表示不修改密码</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">真实名称 <span class="color-red"></span></label>
                    <div class="layui-input-inline ">
                        <input type="text" class="layui-input " maxlength="20" name="User[realname]" value="{{$user->realname ?? ''}}" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">联系电话 <span class="color-red"></span></label>
                    <div class="layui-input-inline ">
                        <input type="text" class="layui-input " maxlength="20" name="User[telephone]" value="{{$user->telephone ?? ''}}" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">电子邮件 <span class="color-red"></span></label>
                    <div class="layui-input-inline ">
                        <input type="text" class="layui-input " maxlength="50" name="User[email]" value="{{$user->email ?? ''}}" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">地址 <span class="color-red"></span></label>
                    <div class="layui-input-inline ">
                        <input type="text" class="layui-input " maxlength="100" name="User[address]" value="{{$user->address ?? ''}}" >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">性别</label>
                    <div class="layui-input-block">
                        @foreach($user->sexItem() as $ind => $item)
                            <input type="radio" name="User[sex]" value="{{$ind}}" @if(isset($user->sex) && $user->sex == $ind) checked
                                   @endif title="{{$item}}">

                        @endforeach
                    </div>
                    <div class="layui-form-mid layui-word-aux "></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所属角色<span class="color-red">*</span></label>
                    <div class="layui-input-block">
                        @foreach($roleAll as $ind => $item)
                            <input type="checkbox" name="role[]" title="{{$item->title}}" value="{{$item->name}}" lay-skin="primary" @if($user->hasRole($item->name)) checked @endif>
                        @endforeach
                    </div>
                    <div class="layui-form-mid layui-word-aux "></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">状态<span class="color-red">*</span></label>
                    <div class="layui-input-block">
                        @foreach($user->statusItem() as $ind => $item)
                                <input type="radio" name="User[status]" value="{{$ind}}" @if(isset($user->status) && $user->status == $ind) checked
                                       @endif title="{{$item}}">
                        @endforeach
                    </div>
                    <div class="layui-form-mid layui-word-aux "></div>
                </div>
                <div class="layui-form-item margin-bottom-submit">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit="" lay-filter="create">立即提交</button>
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

            //日期
            laydate.render({
                elem: '#date',
                trigger: 'click',
                range: true
            });

            //监听提交
            form.on('submit(create)', function (data) {
                if ($("input[name='_method']").val() === 'PUT') {
                    id = $("input[name='id']").val();
                    _url = '/admin/' + MODULE_NAME + '/' + id;
                } else {
                    _url = '/admin/' + MODULE_NAME;
                }
                $.ajax({
                    type: 'POST',
                    url: _url,
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
                                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                                parent.$('button[lay-filter="data-search-btn"]').click();//刷新列表
                                parent.layer.close(index); //再执行关闭

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
                    console.log(data);
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
                    console.log(data);
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