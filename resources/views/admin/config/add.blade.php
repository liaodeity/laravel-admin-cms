@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layuimini-container">
        <div class="layuimini-main">
            <div class="layui-form layuimini-form">
                <form class="layui-form" action="" lay-filter="currentForm" onsubmit="return false;">
                    @method($_method ?? '')
                    @csrf
                    <input type="hidden" name="id" value="{{$config->id ?? ''}}">
                    @if(!isset($config->id))
                        <div class="layui-form-item">
                            <label class="layui-form-label">配置类型 <span class="color-red"></span></label>
                            <div class="layui-input-block">
                                @foreach($config->typeItem() as $ind=>$val)
                                    <input type="radio" name="Config[type]" value="{{$ind}}" title="{{$val}}"
                                           @if(isset($config->type) && $config->type==$ind ) checked @endif >
                                @endforeach
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">配置名称 <span class="color-red"></span></label>
                            <div class="layui-input-block">
                                <input type="text" name="Config[name]" value="{{$config->name ?? ''}}" maxlength="50" autocomplete="off"
                                       placeholder=""
                                       class="layui-input">
                            </div>
                        </div>
                    @endif
                    <div class="layui-form-item">
                        <label class="layui-form-label">配置标题 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <input type="text" name="Config[title]" value="{{$config->title ?? ''}}" maxlength="100" autocomplete="off"
                                   placeholder=""
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">配置内容 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            @switch($config->type)
                                @case(\App\Enums\ConfigTypeEnum::NUM_TYPE)
                                <input type="number" name="Config[content]" value="{{$config->content ?? ''}}" onkeyup="keyupNumber(this.value)" maxlength="65535" autocomplete="off"
                                       placeholder=""
                                       class="layui-input">
                                @break
                                @case(\App\Enums\ConfigTypeEnum::STR_TYPE)
                                <input type="text" name="Config[content]" value="{{$config->content ?? ''}}" onkeyup="keyupNumber(this.value)" maxlength="65535" autocomplete="off"
                                       placeholder=""
                                       class="layui-input">
                                @break
                                @case(\App\Enums\ConfigTypeEnum::ARR_TYPE)
                                <select name="Config[content]">
                                    <option value=""></option>
                                    @foreach($config->getParamItem($config) as $item)
                                        <option value="{{$item->value}}" @if(isset($config->content) && $config->content == $item->value) selected @endif>{{$item->label ?? ''}}</option>
                                    @endforeach
                                </select>
                                @break
                                @case(\App\Enums\ConfigTypeEnum::ITEM_TYPE)
                                <select name="Config[content]">
                                    <option value=""></option>
                                    @foreach($config->getParamItem($config) as $value => $label)
                                        <option value="{{$value}}" @if(isset($config->content) && $config->content == $value) selected @endif>{{$label ?? ''}}</option>
                                    @endforeach
                                </select>
                                @break
                                @case(\App\Enums\ConfigTypeEnum::TEXT_TYPE)
                                <textarea placeholder="" class="layui-textarea" name="Config[content]"
                                          maxlength="65535">{{$config->content ?? ''}}</textarea>
                            @endswitch
                        </div>
                    </div>
                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">说明 <span class="color-red"></span></label>
                        <div class="layui-input-block">
                            <textarea placeholder="" class="layui-textarea" name="Config[desc]" maxlength="200">{{$config->desc ?? ''}}</textarea>
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
    </div>
@endsection

@section('footer')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'layarea', 'upload'], function () {
            var form = layui.form
                , layer = layui.layer
                , layedit = layui.layedit
                , laydate = layui.laydate,
                layarea = layui.layarea;
            var upload = layui.upload;
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
                        // loading = layer.load(2)
                    },
                    complete: function () {
                        $("#button[lay-filter='create']").removeClass('disabled').prop('disabled', false);
                        // layer.close(loading)
                    },
                    error: function () {
                        top.layer.msg(AJAX_ERROR_TIP, {
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
                });
                return false;
            });
            $(document).on('click', '#add_wx', function (data) {
                var index = layer.open({
                    title: '',
                    type: 2,
                    shade: 0.2,
                    maxmin: false,
                    shadeClose: true,
                    area: ['450px', '460px'],
                    content: '/admin/' + MODULE_NAME + '/bind',
                });

                return false;
            });

        });
    </script>
@endsection
