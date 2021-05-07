@extends('layouts.app')
@section('style')
    <style>
        .layui-table img{
            max-width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="layui-fluid">
        <div class="layui-row">
            <table class="layui-table zhihe-show">
                <colgroup>
                    <col width="10%">
                    <col width="">
                    <col>
                </colgroup>
                <thead>
                </thead>
                <tbody>
                <tr>
                    <th>图片路径</th>
                    <td>{{$image->path}}</td>
                </tr>
                <tr>
                    <th>尺寸（长度px*宽度px）</th>
                    <td>{{$image->width_height ?? ''}}</td>
                </tr>
                <tr>
                    <th>大小</th>
                    <td>{{$image->size ?? ''}}</td>
                </tr>
                <tr>
                    <th>预览</th>
                    <td><img src="{{$image->url}}" alt=""></td>
                </tr>
                <tr>
                    <th>最后修改时间</th>
                    <td>{{$image->time}}</td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <button type="button" class="layui-btn layui-btn-primary margin-top-15" id="image_upload">
                            <i class="layui-icon"></i>上传图片替换
                        </button>
                        <p> （上传替换的图片尺寸尽量与原图尺寸保持一致，防止因尺寸导致页面显示问题）</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'layarea', 'table', 'tableSelect','upload'], function () {
            var form = layui.form
                , layer = layui.layer
                , layedit = layui.layedit
                , laydate = layui.laydate,
                layarea = layui.layarea;
            table = layui.layarea;
            upload = layui.upload;
            //日期
            laydate.render({
                elem: '#pass_at',
                trigger: 'click',
                type: 'datetime'
            });
            //日期
            laydate.render({
                elem: '#reply_at',
                trigger: 'click',
                type: 'datetime'
            });
            var uploadInst2 = upload.render({
                elem: '#image_upload' //绑定元素
                , url: '{{url('admin/image_manager/upload')}}?en_path={{$en_path ?? ''}}' //上传接口
                , done: function (res) {
                    //code=0代表上传成功
                    if (res.code === 0) {
                        location.reload();
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
                }
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
