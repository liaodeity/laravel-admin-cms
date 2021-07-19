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
     .layui-form-item .layui-form-checkbox[lay-skin=primary]{
         margin-top: 0;
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
                        <div class="layui-input-block ">
                            @foreach($item['child'] as $val)
                                <input type="checkbox" name="auth[]" data-name="{{$val['name']}}" value="{{$val['name']}}" title="{{$val['title']}}" @if($val['checked']) checked @endif lay-skin="primary">
                            @endforeach
                        </div>
                    </div>
                @endforeach
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
        });
    </script>
@endsection
