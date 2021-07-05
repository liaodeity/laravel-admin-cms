@extends('layouts.app')
@section('style')
@endsection

@section('content')
    <div class="layui-fluid">
        <div class="system-gui-main bg-white system-gui-add">
            <form class="layui-form" action="" lay-filter="currentForm" onsubmit="return false;">
                <div class="layui-tab-item layui-show">
                    @method($_method ?? '')
                    @csrf
                    <input type="hidden" name="id" value="{{$menu->id ?? ''}}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">上级菜单 <span class="color-red"></span></label>
                        <div class="layui-input-block ">
                            <select name="Menu[pid]" lay-filter="survey_id" class=" width-120">
                                <option value=""></option>
                                @foreach($menuPidList as $ind => $item)
                                    <option data-name="{{$item->auth_name ?? ''}}" value="{{$item->id}}"
                                            @if(isset($menu->pid) && $menu->pid == $item->id) selected @endif>|--{{$item->title}}</option>
                                    @if(isset($item->_child))
                                        @foreach($item->_child as $child)
                                            <option data-name="{{$child->auth_name ?? ''}}" value="{{$child->id}}"
                                                    @if(isset($menu->pid) && $menu->pid == $child->id) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;|--{{$child->title}}</option>
                                            @if(isset($child->_child))
                                                @foreach($child->_child as $child2)
                                                    <option data-name="{{$child2->auth_name ?? ''}}" value="{{$child2->id}}"
                                                            @if(isset($menu->pid) && $menu->pid == $child2->id) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--{{$child2->title}}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">菜单名称 <span class="color-red">*</span></label>
                        <div class="layui-input-block">
                            <input type="text" name="Menu[title]" value="{{$menu->title ?? ''}}" maxlength="50" autocomplete="off"
                                   placeholder=""
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">权限名称 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <textarea placeholder="" class="layui-textarea" name="Menu[auth_name]"
                                      maxlength="255">{{$menu->auth_name ?? ''}}</textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">路由地址 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <input type="text" name="Menu[href]" value="{{$menu->href ?? ''}}" maxlength="100" autocomplete="off"
                                   placeholder=""
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">图标 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <input type="text" name="Menu[icon]" value="{{$menu->icon ?? ''}}" maxlength="100" autocomplete="off"
                                   placeholder=""
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">快捷方式 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            @foreach(\App\Enums\SwitchYesEnum::attrs () as $ind=>$val)
                                <input type="radio" name="Menu[is_shortcut]" value="{{$ind}}" title="{{$val}}"
                                       @if(isset($menu->is_shortcut) && $menu->is_shortcut==$ind ) checked @endif >
                            @endforeach
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">排序 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <input type="number" name="Menu[sort]" value="{{$menu->sort ?? 99}}" maxlength="100" autocomplete="off"
                                   placeholder=""
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">状态 <span class="color-red">*</span></label>
                        <div class="layui-input-block">
                            @foreach(\App\Enums\MenuStatusEnum::attrs () as $ind=>$val)
                                <input type="radio" name="Menu[status]" value="{{$ind}}" title="{{$val}}"
                                       @if(isset($menu->status) && $menu->status==$ind ) checked @endif >
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block  padding-bottom-15">
                        <button class="layui-btn" lay-submit="" lay-filter="create">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script type="text/javascript">
        layui.use(['element', 'form', 'jquery', 'layedit', 'laydate', 'systemGui'], function () {
            var form = layui.form
                , element = layui.element
                , layer = layui.layer
                , $ = layui.jquery
                , layedit = layui.layedit
                , systemGui = layui.systemGui
                , laydate = layui.laydate;
            LayerPageIndex = layer.index;
            form.render();
            //监听提交
            //发布时间
            laydate.render({
                elem: '#release_at',
                trigger: 'click'
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
