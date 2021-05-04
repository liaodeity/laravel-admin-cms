layui.define(["element", "jquery"], function (exports) {
    var element = layui.element,
        $ = layui.$,
        layer = layui.layer;

    var systemGui = {
        listLoading() {
            return layer.load(2);
        },
        openIFrame(href, title){
            var openWindow = layer.open ({
                title: '',
                type: 2,
                shade: 0.2,
                maxmin: false,
                shadeClose: false,
                area: [ '65%', '60%' ],
                content: href
            });
            // $(window).on("resize", function () {
            //     if (openWindow)
            //         layer.iframeAuto(openWindow);
            // });
        },
        /**
         * 获取指定链接内容并打开页面
         * @param href
         * @param title
         * @returns {string}
         */
        getHrefContentOpen: function (href, title) {
            var content = '';
            var v = new Date().getTime();
            $.ajax({
                url: href.indexOf("?") > -1 ? href + '&v=' + v : href + '?v=' + v,
                type: 'get',
                dataType: 'html',
                async: true,
                beforeSend: function () {
                    loading = layer.load(2)
                },
                complete: function () {
                    layer.close(loading)
                },
                success: function (data) {
                    content = data;
                    var openWH = miniPage.getOpenWidthHeight();
                    // var openWindow = layer.open({
                    //     title: title,
                    //     type: 1,
                    //     shade: 0.2,
                    //     maxmin: false,
                    //     shadeClose: true,
                    //     area: [openWH[0] + 'px', openWH[1] + 'px'],
                    //     offset: [openWH[2] + 'px', openWH[3] + 'px'],
                    //     content: content,
                    //     end: function () {
                    //         $(window).off("resize");
                    //         windowResize = null;
                    //     }
                    // });
                    var openWindow = layer.open ({
                        title: '',
                        type: 1,
                        shade: 0.2,
                        maxmin: false,
                        shadeClose: false,
                        area: [ '65%', '60%' ],
                        content: content
                    });
                    $(window).on("resize", function () {
                        if (openWindow)
                            layer.iframeAuto(openWindow);
                    });
                },
                error: function (xhr, textstatus, thrown) {
                    return layer.msg('状态码:' + xhr.status + '，' + xhr.statusText + '，请稍后再试！');
                }
            });
        },
        //更新或创建
        createOrUpdate: function (data, method, _url) {
            data._method = method;
            $.ajax({
                type: 'POST',
                url: _url,
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    loading = systemGui.listLoading()
                },
                complete: function () {
                    $("#form-iframe-add button[type='submit']").removeClass('disabled').prop('disabled', false);
                    layer.close(loading)
                },
                error: function () {
                    top.layer.msg(GUI_LANG.FAIL_ACCESS, {
                        icon: 2,
                        time: GUI_LANG.ERROR_TIP_TIME,
                        shade: 0.3
                    });
                },
                success: function (data) {
                    if (data.code === 0) {
                        layer.msg(data.message, {icon: 6, time: GUI_LANG.SUCCESS_TIME, shade: 0.2});
                        if(data.url){
                            if($('#pjax-container').length){
                                $.pjax({url: data.url, container: '#pjax-container'})
                            }else{
                                setTimeout(function () {
                                    location.href = data.url
                                }, GUI_LANG.SUCCESS_TIME);
                            }
                        }else{
                            setTimeout(function () {
                                parent.$('button[lay-filter="data-search-btn"]').click();//刷新列表
                                parent.layer.close(LayerPageIndex); //关闭所有页面层
                            }, GUI_LANG.SUCCESS_TIME);
                        }

                    }else{
                        top.layer.msg(data.message, {
                            icon: 2,
                            time: GUI_LANG.ERROR_TIP_TIME,
                            shade: 0.3
                        });
                    }
                }
            });
        },
        //删除记录
        patchDeleteRaw:function(msg, urls){
            var deleteRow = 0;
            layer.confirm(msg, function (index) {
                layer.close(index);
                urls.forEach(function (val, ind) {
                    deleteRow++;
                    $.ajax({
                        type: 'POST',
                        url: val,
                        data: {
                            _method: 'DELETE'
                        },
                        sync:false,
                        dataType: 'json',
                        beforeSend: function () {

                            loading = systemGui.listLoading()
                        },
                        complete: function () {
                            // $("#form-iframe-add button[type='submit']").removeClass('disabled').prop('disabled', false);
                            layer.close(loading)

                        },
                        error: function () {
                            top.layer.msg('访问失败', {
                                icon: 2,
                                time: GUI_LANG.ERROR_TIP_TIME,
                                shade: 0.3
                            });
                        },
                        success: function (data) {
                            if (data.code === 0) {
                                if(deleteRow >= urls.length){

                                    //所有删除完毕后才提示
                                    layer.msg(data.message, {icon: 6, time: GUI_LANG.SUCCESS_TIME, shade: 0.2});
                                    setTimeout(function () {
                                        $('button[lay-filter="data-search-btn"]').click();//刷新列表

                                    }, GUI_LANG.SUCCESS_TIME);
                                }

                            } else {
                                top.layer.msg(data.message, {
                                    icon: 2,
                                    time: GUI_LANG.ERROR_TIP_TIME,
                                    shade: 0.3
                                });
                            }
                        }
                    });
                })

            });
        },
        deleteRaw: function (msg, url) {
            layer.confirm(msg, function (index) {
                layer.close(index);
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        _method: 'DELETE'
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        loading = systemGui.listLoading()
                    },
                    complete: function () {
                        // $("#form-iframe-add button[type='submit']").removeClass('disabled').prop('disabled', false);
                        layer.close(loading)
                    },
                    error: function () {
                        top.layer.msg('访问失败', {
                            icon: 2,
                            time: GUI_LANG.ERROR_TIP_TIME,
                            shade: 0.3
                        });
                    },
                    success: function (data) {
                        if (data.code === 0) {
                            layer.msg(data.message, {icon: 6, time: GUI_LANG.SUCCESS_TIME, shade: 0.2});
                            setTimeout(function () {
                                $('button[lay-filter="data-search-btn"]').click();//刷新列表

                            }, GUI_LANG.SUCCESS_TIME);
                        } else {
                            top.layer.msg(data.message, {
                                icon: 2,
                                time: GUI_LANG.ERROR_TIP_TIME,
                                shade: 0.3
                            });
                        }
                    }
                });
            });
        },
    }
    exports("systemGui", systemGui);
});
