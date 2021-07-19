@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layui-container">
        <div class="layui-row">
            <form class="layui-form" action="" lay-filter="example" onsubmit="return false;">
                {{ method_field($_method ?? '') }}
                {{csrf_field ()}}
                <input type="hidden" name="id" value="{{$permission->id ?? ''}}">
                <input type="hidden" name="Permission[user_id]" value="{{get_login_user_id ()}}">
                <div class="layui-form-item">
                    <label class="layui-form-label">权限名称 <span class="color-red">*</span></label>
                    <div class="layui-input-block ">
                        <input type="text" class="layui-input " name="Permission[title]" maxlength="50" autocomplete="off"
                               value="{{$permission->title ?? ''}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所属菜单 <span class="color-red"></span></label>
                    <div class="layui-input-block ">
                        <select id="survey_id" name="Permission[menu_id]" lay-filter="survey_id" class=" width-120">
                            <option value=""></option>
                            @foreach($menus as $ind => $item)
                                @if(isset($item->_child))
                                    <optgroup label="{{$item->title}}">
                                    @foreach($item->_child as $child)
                                        @if(!isset($child->_child))
                                                <option data-name="{{$child->auth_name ?? ''}}" value="{{$child->id}}"
                                                        @if(isset($permission->menu_id) && $permission->menu_id == $child->id) selected @endif>{{$child->title}}</option>
                                        @endif
                                    @endforeach
                                    </optgroup>

                                    @foreach($item->_child as $child)
                                        @if(isset($child->_child))
                                            <optgroup label="{{$item->title}}>{{$child->title}}">
                                                @foreach($child->_child as $child2)
                                                    @if(isset($child2->_child))

                                                    @else
                                                        <option data-name="{{$child2->auth_name ?? ''}}" value="{{$child2->id}}"
                                                                @if(isset($permission->menu_id) && $permission->menu_id == $child2->id) selected @endif>{{$child2->title}}</option>
                                                    @endif
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">权限标识 <span class="color-red">*</span></label>
                    <div class="layui-input-block ">
                        <input id="name" type="text" class="layui-input " name="Permission[name]" maxlength="100" autocomplete="off"
                               value="{{$permission->name ?? ''}}">
                        <div class="layui-form-mid layui-word-aux ">
                            （自动会转换成小写，请使用[英文或下划线]标识，常规如“user|user_show|user_create|user_edit|user_delete|user_import|user_export”）
                        </div>
                    </div>
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
