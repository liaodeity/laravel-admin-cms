@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layui-container">
        <div class="layui-row">
            <form class="layui-form" action="" lay-filter="example" onsubmit="return false;">
                {{ method_field($_method ?? '') }}
                {{csrf_field ()}}
                <input type="hidden" name="id" value="{{$page->id ?? ''}}">
                <div class="layui-form-item">
                    <label class="layui-form-label">标题 <span class="color-red">*</span></label>
                    <div class="layui-input-block">
                        <input type="text" name="Page[title]" value="{{$page->title ?? ''}}" maxlength="50" autocomplete="off"
                               placeholder=""
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">英文标识 <span class="color-red"></span></label>
                    <div class="layui-input-inline">
                        <input type="text" name="Page[name]" value="{{$page->name ?? ''}}" maxlength="50" autocomplete="off"
                               placeholder=""
                               class="layui-input">

                    </div>
                    <div class="layui-form-mid layui-word-aux ">英文唯一标识</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">摘要描述 <span class="color-red"></span></label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="Page[desc]" >{{$page->desc}}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">详情内容 <span class="color-red">*</span></label>
                    <div class="layui-input-block">
                        <x-rich-text-form name="Page[content]" :value="$page->content" />
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">状态<span class="color-red">*</span></label>
                    <div class="layui-input-block">
                        @foreach(\App\Enums\StatusEnum::attrs () as $ind => $item)
                            <input type="radio" name="Page[status]" value="{{$ind}}" @if(isset($page->status) && $page->status == $ind) checked
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
        layui.use([ 'form', 'layedit', 'laydate', 'layarea', 'table', 'tableSelect','wangEditor'], function () {
            var form = layui.form
                , layer = layui.layer
                , layedit = layui.layedit
                , laydate = layui.laydate
                , wangEditor = layui.wangEditor
                ,layarea = layui.layarea;
            table = layui.layarea;
            //日期
            laydate.render({
                elem: '#date',
                trigger: 'click',
                range: true
            });
            //<x-rich-text-form name="Page[content]" :value="$page" :js="true" />
            //监听提交
            form.on('submit(create)', function (data) {
                if ($("input[name='_method']").val() === 'PUT') {
                    id = $("input[name='id']").val();
                    _url = '/admin/' + MODULE_NAME + '/' + id;
                } else {
                    _url = '/admin/' + MODULE_NAME;
                }
                // 添加请求拦截器
                axios.interceptors.request.use(function (config) {
                    // 在发送请求之前做些什么
                    return config;
                }, function (error) {
                    // 对请求错误做些什么
                    return Promise.reject(error);
                });

                axios.interceptors.response.use(function (response) {
                    // 对响应数据做点什么
                    return response;
                }, function (error) {
                    // 对响应错误做点什么
                    layer.msg(error.response.data.message, {
                        icon: 2,
                        time: FAIL_TIME,
                        shade: 0.3
                    });
                    return Promise.reject(error);
                });
                axios.post(_url, $(data.form).serialize()).then((response) => {
                    var data = response.data;
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
                });
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
