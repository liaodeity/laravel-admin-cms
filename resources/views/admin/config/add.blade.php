@extends('layouts.app')
@section('style')
    <style>
        .layui-tab-title li {
            /*width: 47%;*/
        }

        .set_weixin {
            background: #fff;
            font-size: 14px;
            padding: 20px;
            overflow: hidden;
            text-align: center;
        }

        .set_weixin p.title {
            padding: 5px 10px;
            margin: 0;
            color: #47568d;
            font-size: 16px;
            font-weight: bold;
        }

        .set_weixin p.title_wx {
            padding: 5px 0;
            text-indent: 30px;
            text-align: left;
            line-height: 26px;
        }

        .wx_code {
            text-align: center;
            padding: 10px;
        }

        .wx_code img {
            width: 140px;
            height: 140px;
        }

        div.wx_list {
            float: left;
            position: relative;
            border: 1px solid #ebebeb;
            padding: 10px;
            margin-right: 10px;
            text-align: center;
            color: #0C0C0C;
        }

        div.wx_list:hover {
            color: #666
        }

        .icon_wxBg {
            margin: 5px auto;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #f2f2f2;
            text-align: center;
        }

        .icon_wxBg i {
            font-size: 40px;
            line-height: 100px;
            color: #c9c9c9;
            margin: 0 auto;
        }

        .close_wx {
            position: absolute;
            right: 0;
            top: 0px;
            font-size: 12px;
            width: 20px;
            height: 20px;
            line-height: 20px;
            font-weight: bold;
        }

        .close_wx i {
            font-size: 22px;
            color: red;
            cursor: pointer;
        }

        .icon_wxHead {
            text-align: center;
            margin: 5px auto;
            width: 100px;
            height: 100px;
        }

        .icon_wxHead img {
            width: 98px;
            height: 98px;
            border-radius: 50%;
            border: 1px solid #ebebeb;
        }

        #add_wx {
            cursor: pointer;
        }

        .pic-item {
            position: relative;
            float: left;
            margin-right: 10px;
            margin-bottom: 10px;
            max-width: 320px;
            max-height: 200px;
        }

        .pic-item i {
            position: absolute;
            color: red;
            font-size: 40px;
            right: 0;
            top: 0;
            border: 1px solid red;
            border-radius: 50%;
            cursor: pointer;
        }
        .layui-form-label{
            width:200px;
        }
        .layui-input-block{
            margin-left: 240px;
        }
    </style>
@endsection

@section('content')

    <div class="layui-container">
        <div class="layui-row">
            <form class="layui-form" action="" lay-filter="createForm">
                {{method_field ('PUT')}}
                <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                    <ul class="layui-tab-title">
                        <li class="layui-this layui-col-sm2">基本信息</li>
                        <li class="layui-col-sm2">联系信息</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <input type="hidden" name="id" value="{{$config->id ?? ''}}">
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">网站标题<span class="color-red">*</span></label>
                                <div class="layui-input-block">
                                    <input placeholder="" class="layui-input" autocomplete="off" maxlength="100" name="Config[seo_title]" value="{{$config->seo_title ?? ''}}">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">网站名称 <span class="color-red">*</span></label>
                                <div class="layui-input-block">
                                    <input type="text" name="Config[site_name]" autocomplete="off" placeholder="" maxlength="100" value="{{$config->site_name ?? ''}}"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">公司名称 <span class="color-red">*</span></label>
                                <div class="layui-input-block">
                                    <input type="text" name="Config[company_name]" autocomplete="off" maxlength="100" placeholder=""
                                           value="{{$config->company_name ?? ''}}"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">公司简称 <span class="color-red">*</span></label>
                                <div class="layui-input-block">
                                    <input type="text" name="Config[sort_name]" autocomplete="off" maxlength="50" placeholder=""
                                           value="{{$config->sort_name ?? ''}}"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">备案号 <span class="color-red"></span></label>
                                <div class="layui-input-block">
                                    <input type="text" name="Config[icp]" autocomplete="off" maxlength="200" placeholder=""
                                           value="{{$config->icp ?? ''}}"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">图片水印文字 <span class="color-red"></span></label>
                                <div class="layui-input-block">
                                    <input type="text" name="Config[watermark_text]" autocomplete="off" maxlength="100" placeholder=""
                                           value="{{$config->watermark_text ?? ''}}"
                                           class="layui-input">
                                </div>
                            </div>

                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">联系方式</label>
                                <div class="layui-input-inline width-200" style="margin-left: 10px;">
                                    <input type="text" name="Config[contact_name]" maxlength="50" value="{{$config->contact_name ?? ''}}" placeholder="请输入联系人"
                                           autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-input-inline width-200">
                                    <input type="text" name="Config[telephone]" value="{{$config->telephone ?? ''}}"
                                           placeholder="请输入电话" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">公司邮箱 <span class="color-red"></span></label>
                                <div class="layui-input-block">
                                    <input type="text" name="Config[email]" autocomplete="off" maxlength="50" placeholder=""
                                           value="{{$config->email ?? ''}}"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">传真 <span class="color-red"></span></label>
                                <div class="layui-input-block">
                                    <input type="text" name="Config[fax]" autocomplete="off" maxlength="50" placeholder=""
                                           value="{{$config->fax ?? ''}}"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">地址 <span class="color-red"></span></label>
                                <div class="layui-input-block">
                                    <input type="text" name="Config[address]" autocomplete="off" maxlength="150" placeholder=""
                                           value="{{$config->address ?? ''}}"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">地图名片 <span class="color-red"></span></label>
                                <div class="layui-input-block">
                                    <input type="text" name="Config[map_card_url]" autocomplete="off" maxlength="150" placeholder=""
                                           value="{{$config->map_card_url ?? ''}}"
                                           class="layui-input">
                                    <div class="layui-form-mid layui-word-aux ">
                                        选择显示的内容：
                                        不勾选显示内容；
                                        设置地图区域大小：
                                        自定义：尺寸：（宽）566 x（高）223；名片生成地址：<a href="http://api.map.baidu.com/mapCard/setInformation.html" target="_blank">http://api.map.baidu.com/mapCard/setInformation.html</a>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="layui-form-item margin-bottom-submit">
                            <div class="layui-input-block">
                                @if( check_admin_auth ($MODULE_NAME.' edit'))
                                <button class="layui-btn" lay-submit="" lay-filter="create">立即提交</button>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
