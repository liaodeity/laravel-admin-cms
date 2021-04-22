@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layuimini-container">
        <div class="layuimini-main">
            <div class="layui-form layuimini-form">
                @foreach($configs as $config)
                    <div class="layui-form-item">
                        <label class="layui-form-label required">{{$config->title}}</label>
                        <div class="layui-input-block">
                            @switch($config->type)
                                @case(\App\Models\Config::NUM_TYPE)
                                <input type="number" name="Config[{{$config->id}}]" value="{{$config->content ?? ''}}" onkeyup="keyupNumber(this.value)" maxlength="65535" autocomplete="off"
                                       placeholder=""
                                       class="layui-input">
                                @break
                                @case(\App\Models\Config::STR_TYPE)
                                <input type="text" name="Config[{{$config->id}}]" value="{{$config->content ?? ''}}" onkeyup="keyupNumber(this.value)" maxlength="65535" autocomplete="off"
                                       placeholder=""
                                       class="layui-input">
                                @break
                                @case(\App\Models\Config::ARR_TYPE)
                                @case(\App\Models\Config::ITEM_TYPE)
                                <select name="Config[{{$config->id}}]">
                                    <option value=""></option>
                                    @foreach($config->getParamItem($config) as $item)
                                        <option value="{{$item->value}}" @if(isset($config->content) && $config->content == $item->value) selected @endif>{{$item->label ?? ''}}</option>
                                    @endforeach
                                </select>
                                @break
                                @case(\App\Models\Config::TEXT_TYPE)
                                <textarea placeholder="" class="layui-textarea" name="Config[{{$config->id}}]"
                                          maxlength="65535">{{$config->content ?? ''}}</textarea>
                            @endswitch
                            @if($config->description)
                                <tip>ddfs</tip>
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
        function getWxUserLoad(html) {
            $(".account_uploadSet .user-row").remove();
            $("#add_wx").before(html)
        }

        layui.use(['form', 'layedit', 'laydate', 'layarea', 'upload'], function () {
            var form = layui.form
                , layer = layui.layer
                , layedit = layui.layedit
                , laydate = layui.laydate,
                layarea = layui.layarea;

            var upload = layui.upload;

            //上传LOGO
            var uploadInst = upload.render({
                elem: '#logo_upload' //绑定元素
                , url: '{{route('upload.image')}}' //上传接口
                , done: function (res) {
                    //code=0代表上传成功
                    if (res.code === 0) {
                        $("#logo_id").val(res.data.id)
                        $("#logo_src").attr('src', res.data.src).removeClass('layui-hide')
                    } else {
                        top.layer.msg(res.msg, {
                            icon: 2,
                            time: FAIL_TIME,
                            shade: 0.3
                        });
                    }
                }
                , error: function () {
                    //请求异常回调
                }
            });
            var uploadInst2 = upload.render({
                elem: '#mp_upload' //绑定元素
                , url: '{{route('upload.image')}}' //上传接口
                , done: function (res) {
                    //code=0代表上传成功
                    if (res.code === 0) {
                        $("#mp_id").val(res.data.id)
                        $("#mp_src").attr('src', res.data.src).removeClass('layui-hide')
                    } else {
                        top.layer.msg(res.msg, {
                            icon: 2,
                            time: FAIL_TIME,
                            shade: 0.3
                        });
                    }
                }
                , error: function () {
                    //请求异常回调
                }
            });
            var uploadInst3 = upload.render({
                elem: '#pic_upload' //企业环境图片
                , url: '{{route('upload.image')}}' //上传接口
                , done: function (res) {
                    //code=0代表上传成功
                    if (res.code === 0) {
                        $("#pic_upload_items").append('<div class="pic-item">\n' +
                            '                                            <i class="remove-pic iconfont iconchushaixuanxiang" title="删除该图片"></i>\n' +
                            '                                            <input type="hidden" name="ConfigPic[]" value="' + res.data.id + '">\n' +
                            '                                            <img class="layadmin-homepage-pad-img "\n' +
                            '                                                 src="' + res.data.src + '" width="320"\n' +
                            '                                                 height="200"\n' +
                            '                                            >\n' +
                            '                                        </div>');
                    } else {
                        top.layer.msg(res.msg, {
                            icon: 2,
                            time: FAIL_TIME,
                            shade: 0.3
                        });
                    }
                }
                , error: function () {
                    //请求异常回调
                }
            });

            $(document).on('click', '.remove-pic', function () {
                self = this;
                layer.msg('是否删除图片？', {
                    time: 0 //不自动关闭
                    , btn: ['删除', '取消']
                    , yes: function (index) {
                        layer.close(index);
                        $(self).parent('.pic-item').remove();
                    }
                });

            });

            //日期
            // laydate.render({
            //     elem: '#date'
            // });
            // laydate.render({
            //     elem: '#date1'
            // });
            $(".close_wx").on('click', function () {
                $(this).parent('.wx_list').remove()
            })
            //监听提交
            form.on('submit(create)', function (data) {
                // layer.alert(JSON.stringify(data.field), {
                //     title: '最终的提交信息'
                // })
                // layer.msg('更新成功', {icon: 6});
                // console.log(data);
                $.ajax({
                    type: 'POST',
                    url: '/admin/' + MODULE_NAME + '/' + data.field.id,
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
                            layer.msg(data.msg, {icon: 6, time: SUCCESS_TIME, shade: 0.2});
                            setTimeout(function () {
                                location.reload()
                            }, SUCCESS_TIME);
                        } else {
                            top.layer.msg(data.msg, {
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
            $(document).on('click', '.close_wx', function () {
                id = $(this).data('id');
                that = this;
                layer.confirm('确认是否删除', function (index) {
                    layer.close(index)
                    $.ajax({
                        type: 'post',
                        url: '/admin/' + MODULE_NAME + '/unbind',
                        data: {
                            id: id,
                            _token: '{{csrf_token ()}}'
                        },
                        success: function (data) {
                            if (data.code === 0) {
                                layer.msg(data.msg, {icon: 6, time: SUCCESS_TIME, shade: 0.2});
                                setTimeout(function () {
                                    $(that).parents('.wx_list').remove();
                                }, SUCCESS_TIME);

                            } else {
                                top.layer.msg(data.msg, {
                                    icon: 2,
                                    time: FAIL_TIME,
                                    shade: 0.3
                                });
                            }
                        }
                    })
                });
            })

        });
    </script>
@endsection
