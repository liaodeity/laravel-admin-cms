@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layuimini-container">
        <div class="layuimini-main">
            <div class="layui-form layuimini-form">
                @foreach($configs as $config)
                    <div class="layui-form-item">
                        <label class="layui-form-label ">{{$config->title}}</label>
                        <div class="layui-input-block">
                            @switch($config->type)
                                @case(\App\Enums\ConfigTypeEnum::NUM_TYPE)
                                <input type="number" name="Config[{{$config->id}}]" value="{{$config->content ?? ''}}" onkeyup="keyupNumber(this.value)" maxlength="65535" autocomplete="off"
                                       placeholder=""
                                       class="layui-input">
                                @break
                                @case(\App\Enums\ConfigTypeEnum::STR_TYPE)
                                <input type="text" name="Config[{{$config->id}}]" value="{{$config->content ?? ''}}" onkeyup="keyupNumber(this.value)" maxlength="65535" autocomplete="off"
                                       placeholder=""
                                       class="layui-input">
                                @break
                                @case(\App\Enums\ConfigTypeEnum::ARR_TYPE)
                                <select name="Config[{{$config->id}}]">
                                    <option value=""></option>
                                    @foreach($config->getParamItem($config) as $item)
                                        <option value="{{$item->value}}" @if(isset($config->content) && $config->content == $item->value) selected @endif>{{$item->label ?? ''}}</option>
                                    @endforeach
                                </select>
                                @break
                                @case(\App\Enums\ConfigTypeEnum::ITEM_TYPE)
                                <select name="Config[{{$config->id}}]">
                                    <option value=""></option>
                                    @foreach($config->getParamItem($config) as $value => $label)
                                        <option value="{{$value}}" @if(isset($config->content) && $config->content == $value) selected @endif>{{$label ?? ''}}</option>
                                    @endforeach
                                </select>
                                @break
                                @case(\App\Enums\ConfigTypeEnum::TEXT_TYPE)
                                <textarea placeholder="" class="layui-textarea" name="Config[{{$config->id}}]"
                                          maxlength="65535">{{$config->content ?? ''}}</textarea>
                            @endswitch
                            @if($config->description)
                                <tip>{{$config->description}}</tip>
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="setting">确认保存</button>
                    </div>
                </div>
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
            form.render();

            var upload = layui.upload;
            //监听提交
            form.on('submit(setting)', function (data) {
                // layer.alert(JSON.stringify(data.field), {
                //     title: '最终的提交信息'
                // })
                // layer.msg('更新成功', {icon: 6});
                // console.log(data);
                $.ajax({
                    type: 'POST',
                    url: '/admin/' + MODULE_NAME,
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
                                location.reload()
                            }, SUCCESS_TIME);
                        } else {
                            top.layer.msg(data.message, {
                                icon: 2,
                                time: FAIL_TIME,
                                shade: 0.3
                            });
                        }
                    }
                });
                return false;
            });

        });
    </script>
@endsection
