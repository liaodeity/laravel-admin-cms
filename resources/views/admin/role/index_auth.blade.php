@extends('layouts.app')
@section('style')
<style>
    .layui-form-item{
        margin: 0;
    }
    .layui-form-item .layui-input-block{
        padding: 9px 0;
        min-height: inherit;
    }
    .layui-badge, .layui-badge-dot, .layui-badge-rim{
        font-size: 14px;
        margin: 3px;
    }
</style>
@endsection

@section('content')
    <div class="layui-container">
        <div class="layui-row">
            <form class="layui-form" action="" lay-filter="example" onsubmit="return false;">
                {{ method_field($_method ?? '') }}
                {{csrf_field ()}}
                <input type="hidden" name="id" value="{{$role->id ?? ''}}">
                <input type="hidden" name="Auth[user_id]" value="{{get_login_user_id ()}}">
                @foreach($auths as $ind => $item)
                <div class="layui-form-item">
                    <label class="layui-form-label">{{$item['menu_name']}} <span class="color-red"></span></label>
                    <div class="layui-input-block">
                        @foreach($item['child'] as $val)
                           <span class="layui-badge-rim">{{$val['title']}}[<span onclick="copySelectTest(this)">{{$val['name']}}</span>]</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script type="text/javascript">
        function selectElementText(el) {
            if (document.selection) {   // IE8 以下处理
                var oRange = document.body.createTextRange();
                oRange.moveToElementText(el);
                oRange.select();
            } else {
                var range = document.createRange(); // create new range object
                range.selectNodeContents(el); // set range to encompass desired element text
                var selection = window.getSelection(); // get Selection object from currently user selected text
                selection.removeAllRanges(); // unselect any user selected text (if any)
                selection.addRange(range); // add range to Selection object to select it
            }
        }

        function copySelectionText() {
            var copysuccess; // var to check whether execCommand successfully executed
            try {
                copysuccess = document.execCommand("copy"); // run command to copy selected text to clipboard
            } catch (e) {
                copysuccess = false;
            }
            return copysuccess;
        }

        function copySelectTest(e) {
            selectElementText(e); // select the element's text we wish to read
            var copysuccess = copySelectionText();
            if (copysuccess) {
                layer.msg('已成功复制:'+$(e).text())
            }
        }
    </script>
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
                elem: '#task_date',
                trigger: 'click',
                type: 'date',
                range: '至'
            });
            //监听提交
            form.on('submit(create)', function (data) {
                if ($("input[name='_method']").val() === 'PUT') {
                    id = $("input[name='id']").val();
                    _url = '/admin/' + MODULE_NAME + '/auth/' + id;
                } else {
                    _url = '/admin/' + MODULE_NAME + '/auth/add';
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
            form.on('select(survey_id)', function(data){
                var m = $(data.elem).find('option:selected').data('module');
                $("#name").val(m);
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
