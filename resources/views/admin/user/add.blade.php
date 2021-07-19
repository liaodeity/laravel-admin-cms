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
        });
    </script>
@endsection
