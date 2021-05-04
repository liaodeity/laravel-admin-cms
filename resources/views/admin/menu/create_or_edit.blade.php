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
                        <label class="layui-form-label">父ID <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <input type="text" name="Menu[pid]" value="{{$menu->pid ?? ''}}" maxlength="36" autocomplete="off"
                                   placeholder=""
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">菜单标识名称 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <input type="text" name="Menu[cate_module]" value="{{$menu->cate_module ?? ''}}" maxlength="100" autocomplete="off"
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
                        <label class="layui-form-label">类型 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            @foreach(\App\Enums\MenuTypeEnum::attrs () as $ind=>$val)
                                <input type="radio" name="Menu[type]" value="{{$ind}}" title="{{$val}}"
                                       @if(isset($menu->type) && $menu->type==$ind ) checked @endif >
                            @endforeach
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">排序 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <input type="text" name="Menu[sort]" value="{{$menu->sort ?? ''}}" maxlength="" autocomplete="off"
                                   placeholder=""
                                   class="layui-input">
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
                        <label class="layui-form-label">菜单名称 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <input type="text" name="Menu[title]" value="{{$menu->title ?? ''}}" maxlength="50" autocomplete="off"
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
                            <input type="number" name="Menu[sort]" value="{{$menu->sort ?? ''}}" maxlength="100" autocomplete="off"
                                   placeholder=""
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">状态 <span class="color-red"></span></label>
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
                        <button class="layui-btn" lay-submit="" lay-filter="form{{$_method}}">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    {{--    @include('common/ueditor',['name'=>'content'])--}}
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

            //新建
            form.on('submit(formPOST)', function (data) {
                systemGui.createOrUpdate(data.field, 'POST', '{{url('admin/menu')}}')
                return false;
            });
            //更新
            form.on('submit(formPUT)', function (data) {
                systemGui.createOrUpdate(data.field, 'PUT', '{{url('admin/menu',$menu->id)}}')

                return false;
            });
        });
    </script>
@endsection
