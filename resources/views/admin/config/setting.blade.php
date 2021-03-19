@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layuimini-container">
        <div class="layuimini-main">
            <div class="layui-form layuimini-form">
                <div class="layui-form-item">
                    <label class="layui-form-label required">网站名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="sitename" lay-verify="required" lay-reqtext="网站域名不能为空" placeholder="请输入网站名称"  value="layuimini" class="layui-input">
                        <tip>填写自己部署网站的名称。</tip>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label required">网站域名</label>
                    <div class="layui-input-block">
                        <input type="text" name="domain" lay-verify="required" lay-reqtext="网站域名不能为空" placeholder="请输入网站域名"  value="http://layuimini.99php.cn" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">缓存时间</label>
                    <div class="layui-input-inline" style="width: 80px;">
                        <input type="text" name="cache" lay-verify="number" value="0" class="layui-input">
                    </div>
                    <div class="layui-input-inline layui-input-company">分钟</div>
                    <div class="layui-form-mid layui-word-aux">本地开发一般推荐设置为 0，线上环境建议设置为 10。</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">最大文件上传</label>
                    <div class="layui-input-inline" style="width: 80px;">
                        <input type="text" name="cache" lay-verify="number" value="2048" class="layui-input">
                    </div>
                    <div class="layui-input-inline layui-input-company">KB</div>
                    <div class="layui-form-mid layui-word-aux">提示：1 M = 1024 KB</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">上传文件类型</label>
                    <div class="layui-input-block">
                        <input type="text" name="cache" value="png|gif|jpg|jpeg|zip|rar" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label required">首页标题</label>
                    <div class="layui-input-block">
                        <textarea name="title" class="layui-textarea">layuimini 简洁易用后台管理模板</textarea>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">META关键词</label>
                    <div class="layui-input-block">
                        <textarea name="keywords" class="layui-textarea" placeholder="多个关键词用英文状态 , 号分割"></textarea>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">META描述</label>
                    <div class="layui-input-block">
                        <textarea name="descript" class="layui-textarea">layuimini，最简洁、清爽、易用的layui后台框架模板。</textarea>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label required">版权信息</label>
                    <div class="layui-input-block">
                        <textarea name="copyright" class="layui-textarea">© 2019 layuimini.99php.cn MIT license</textarea>
                    </div>
                </div>
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
