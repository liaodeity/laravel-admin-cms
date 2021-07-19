@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layui-container">
        <div class="layui-row">
            <form class="layui-form" action="" lay-filter="example" onsubmit="return false;">
                {{csrf_field ()}}
                <input type="hidden" name="id" value="{{$user->id ?? ''}}">
                <div class="layui-form-item">
                    <label class="layui-form-label">用户名 <span class="color-red">*</span></label>
                    <div class="layui-input-inline ">
                        <input type="text" class="layui-input layui-disabled" name="User[name]" disabled="disabled" value="{{$user->name ?? ''}}" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">原密码 <span class="color-red"></span></label>
                    <div class="layui-input-inline ">
                        <input type="password" class="layui-input " maxlength="64" name="User[old_pwd]" value="" >
                    </div>
                    <div class="layui-form-mid layui-word-aux "></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">新密码 <span class="color-red"></span></label>
                    <div class="layui-input-inline ">
                        <input type="password" class="layui-input " maxlength="64"  name="User[new_pwd]" value="" >
                    </div>
                    <div class="layui-form-mid layui-word-aux "></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">确认密码 <span class="color-red"></span></label>
                    <div class="layui-input-inline ">
                        <input type="password" class="layui-input " maxlength="64"  name="User[new_pwd2]" value="" >
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
                    url: '{{url('admin/user/password')}}',
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
                            layer.alert(data.message, {icon: 6, shade: 0.2},function () {
                                top.location.href = '{{url('admin/main/logout')}}';
                            });
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
