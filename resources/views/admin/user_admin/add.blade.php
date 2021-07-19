@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layui-container">
        <div class="layui-row">
            <form class="layui-form" action="" lay-filter="example" onsubmit="return false;">
                {{ method_field($_method ?? '') }}
                {{csrf_field ()}}
                <input type="hidden" name="id" value="{{$user->admin->id ?? ''}}">
                @include('admin.user.form_user_add')
                @include('admin.user.form_user_info_add')
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
                        @foreach(\App\Libs\Parameter::userStatusItem () as $ind => $item)
                                <input type="radio" name="UserAdmin[status]" value="{{$ind}}" @if(isset($user->admin->status) && $user->admin->status == $ind) checked
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
        layui.use([ 'form', 'layedit', 'laydate', 'layarea', 'table', 'tableSelect'], function () {
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
        });
    </script>
@endsection
