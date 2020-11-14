const ERROR_TIP_TIME = 3000;//错误提示时间
const SUCCESS_TIP_TIME = 3000;//成功提示时间
$.ajaxSetup ({
  headers: {
    'X-CSRF-TOKEN': $ ('meta[name="csrf-token"]').attr ('content')
  }
});
$(function () {

    //列表排序
    $(".list-data-table.active").on('click', 'thead th.sorting,thead th.sorting_desc,thead th.sorting_asc', function () {
        $(".list-data-table.active .check-all").prop('checked', false);
        $(".list-data-table.active .check-item").prop('checked', false);
        set_check_item_id()
        var field = $(this).data('field');
        var order_by = 'desc';
        if ($(this).hasClass('sorting')) {
            //无->降序
            order_by = 'desc'
            $(this).removeClass('sorting').addClass('sorting_desc')
        } else if ($(this).hasClass('sorting_desc')) {
            //降序->升序
            order_by = 'asc'
            $(this).removeClass('sorting_desc').addClass('sorting_asc')
        } else if ($(this).hasClass('sorting_asc')) {
            //升序->无
            order_by = ''
            $(this).removeClass('sorting_asc').addClass('sorting')
        }
        $(".list-search-form.active").find("input[name='_order_by']").remove();
        if (field && order_by)
            $(".list-search-form.active").append('<input type="hidden" name="_order_by" value="' + field + ' ' + order_by + '"/>');
        $(".list-search-form.active").find("button[type='submit']").click();//触发搜索按钮
    });

    //列表搜索按钮触发查询
    $(".list-search-form.active button[type='submit']").click(function () {
        var query = $(".list-search-form.active").serialize();
        // console.log(query);
    });

    function set_check_item_id() {
        var id = []
        $(".list-data-table.active .check-item:checked").not(':disabled').each(function () {
            id.push($(this).val())
        });
        str_id = id.join(',')
        $(".list-search-form.active").find("input[name='_id']").remove();
        $(".list-search-form.active").append('<input type="hidden" name="_id" value="' + str_id + '"/>');
    }

    //列表勾选全勾
    $(".list-data-table.active").on('click', '.check-all', function () {
        var checked = this.checked;
        // console.log(checked);
        $(".list-data-table.active").find('.check-item').not(':disabled').each(function () {
            $(this).not(':disabled').prop('checked', checked)
        })
        set_check_item_id()
    });
    //列表单选
    $(".list-data-table.active").on('click', '.check-item', function () {
        var checked = this.checked;
        if (checked) {
            var item_all_length = $(".list-data-table.active .check-item").not(':disabled').length;
            var item_check_length = $(".list-data-table.active .check-item:checked").not(':disabled').length;
            if (item_all_length === item_check_length) {
                $(".list-data-table.active .check-all").not(':disabled').prop('checked', true)
            } else {
                $(".list-data-table.active .check-all").not(':disabled').prop('checked', false)
            }

        } else {
            $(".list-data-table.active .check-all").not(':disabled').prop('checked', checked)
        }
        set_check_item_id()
    });

    /**
     * 获取地址中的get参数
     * @param par
     * @param url
     * @returns {*}
     */
    function getPar(par, url) {
        //获取当前URL
        var local_url = url;
        //获取要取得的get参数位置
        var get = local_url.indexOf(par + "=");
        if (get == -1) {
            return false;
        }
        //截取字符串
        var get_par = local_url.slice(par.length + get + 1);
        //判断截取后的字符串是否还有其他get参数
        var nextPar = get_par.indexOf("&");
        if (nextPar != -1) {
            get_par = get_par.slice(0, nextPar);
        }
        return get_par;
    }

    // 分页 start
    $("#list-page").on("click", ".pagination a", function () {
        var id = parseInt(getPar('page', $(this).attr('href')));
        console.log(id);
        $("#current-page").remove();
        str = '<input type="hidden" name="page" id="current-page" value="' + id + '">';
        $(".list-search-form.active").append(str)
        $(".list-search-form.active").find("button[type='submit']").click();//触发搜索按钮
        return false;
    });
    // 分页 end

});
LiaoForm = {
    //获取已选中的列表ID
    get_check_item_id_str: function () {
        var id = $(".list-search-form.active input[name='_id']").val()
        return id;
    }
};

//加载效果
function list_loading() {
    return layer.load(2);
}

function getDataList() {
    var query = $(".list-search-form.active").serialize();
    var url = $(".list-search-form.active").attr('action');
    var loading = null;
    $.ajax({
        type: "get",
        url: url,
        data: query,
        dataType: 'json',
        beforeSend: function () {
            loading = list_loading();
        },
        complete: function () {
            layer.close(loading);
        },
        success: function (data) {
            layer.close(loading);
            if (data.error !== true) {
                $(".list-data-table.active .check-all").prop('checked', false);
                $(".list-data-table.active tbody").html(data.html)
                $("#list-page .page-total").text(data.total)
                $("#list-page .page-nav").html(data.page)
                if(data.html == ''){
                    //无数据显示
                    var colspan = $(".list-data-table.active thead tr>th").length;
                    $(".list-data-table.active tbody").html('<tr><td colspan="'+colspan+'" class="no-data-text">数据不存在</td></tr>')
                }
            }
        },
        error: function () {
            top.layer.msg('网络访问失败', {
                icon: 2,
                time: ERROR_TIP_TIME,
                shade: 0.3
            });
        }
    })
}

function view_fun(title, url) {
    var index = layer.open({
        type: 2,
        title: title,
        content: url
    });
    layer.full(index);
}


// 导出
function export_fun(title, url) {
    if ($("#search-form").length) {
        var query = $("#search-form").serialize();
        window.location.href = url + '?' + query;

    }
}

// 添加
function add_fun(title, url) {
    var index = layer.open({
        type: 2,
        title: title,
        content: url,
        end: function () {
            getDataList();
        }
    });
    layer.full(index);
}

// 修改
function edit_fun(title, url) {
    var index = layer.open({
        type: 2,
        title: title,
        content: url,
        end: function () {
            getDataList();
        }
    })
    layer.full(index);
}

function dialog_fun(title, url, width, height) {
    var index = layer.open({
        id: 'dialog_fun',
        type: 2,
        area: ['80%', '65%'],
        fix: false, //不固定
        maxmin: false,
        shade: 0.4,
        shadeClose: false,
        title: title,
        content: url,
        end: function () {
            getDataList();
        }
    });
    layer.full(index);
}

function parent_dialog_fun(title, url) {
    var index = parent.layer.open({
        type: 2,
        area: ['80%', '65%'],
        fix: false, //不固定
        maxmin: false,
        shade: 0.4,
        shadeClose: false,
        title: title,
        content: url,
        end: function () {
            getDataList();
        }
    });
    layer.full(index);
}

// 提示确认框
function confirm_fun(title, url, text) {
    text = text == undefined ? '是否操作？' : text;
    top.layer.confirm(text, {
        icon: 3,
        title: title
    }, function (index) {
        $.ajax({
            type: "POST",
            url: url,
            data: '',
            dataType: 'json',
            beforeSend: function () {
                // 加载效果
                loading = list_loading()
            },
            complete:function(){
                layer.close(loading)
            },
            error: function () {
                top.layer.msg('网络访问失败', {
                    icon: 2,
                    time: ERROR_TIP_TIME,
                    shade: 0.3
                });
            },
            success: function (data) {
                if (data.error !== true) {
                    top.layer.msg(data.message, {
                        icon: 1,
                        time: SUCCESS_TIP_TIME,
                        shade: 0.3
                    });
                    setTimeout("getDataList()", SUCCESS_TIP_TIME);
                } else {
                    top.layer.msg(data.message, {
                        icon: 2,
                        time: ERROR_TIP_TIME,
                        shade: 0.3
                    });
                }
            }
        })
    });
}

//删除操作
function delete_confirm_fun(title, url, text) {
    text = text == undefined ? '是否操作？' : text;
    top.layer.confirm(text, {
        icon: 3,
        title: title
    }, function (index) {
        $.ajax({
            type: "POST",
            url: url,
            data: {_method: "DELETE"},
            dataType: 'json',
            error: function () {
                top.layer.msg('网络访问失败', {
                    icon: 2,
                    time: ERROR_TIP_TIME,
                    shade: 0.3
                });
            },
            success: function (data) {
                if (data.error !== true) {
                    top.layer.msg(data.message, {
                        icon: 1,
                        time: SUCCESS_TIP_TIME,
                        shade: 0.3
                    });
                    setTimeout("getDataList()", SUCCESS_TIP_TIME);
                } else {
                    top.layer.msg(data.message, {
                        icon: 2,
                        time: ERROR_TIP_TIME,
                        shade: 0.3
                    });
                }
            }
        })
    });
}

//批量确认
function confirm_patch_fun(title, url, text) {
    text = text == undefined ? '是否操作？' : text;
    top.layer.confirm(text, {
        icon: 3,
        title: title
    }, function (index) {
        var query = LiaoForm.get_check_item_id_str()

        $.ajax({
            type: "post",
            url: url,
            data: "id=" + query,
            dataType: 'json',
            error: function () {
                top.layer.msg('网络访问失败', {
                    icon: 2,
                    time: ERROR_TIP_TIME,
                    shade: 0.3
                });
            },
            success: function (data) {
                if (data.error !== true) {
                    top.layer.msg(data.message, {
                        icon: 1,
                        time: SUCCESS_TIP_TIME,
                        shade: 0.3
                    });
                    setTimeout("getDataList()", SUCCESS_TIP_TIME);
                } else {
                    top.layer.msg(data.message, {
                        icon: 2,
                        time: ERROR_TIP_TIME,
                        shade: 0.3
                    });
                }
            }
        })
    });
}

//确认导出数据
function confirm_export_fun(title, url, text) {
    text = text == undefined ? '是否操作？' : text;
    top.layer.confirm(text, {
        icon: 3,
        title: title
    }, function (index) {
        var query = $(".list-search-form.active").serialize();
        var str = url.indexOf('?') > -1 ? '&' : '?';
        go_url = url + str +'' + query;
        top.layer.close(index)
        location.href = go_url;
    });
}

//批量删除
function delete_patch_fun(title, url, text) {
    text = text == undefined ? '是否操作？' : text;
    top.layer.confirm(text, {
        icon: 3,
        title: title
    }, function (index) {
        var query = LiaoForm.get_check_item_id_str()

        $.ajax({
            type: "POST",
            url: url,
            data: {_method: "DELETE", id: query},
            dataType: 'json',
            success: function (data) {
                if (data.error !== true) {
                    top.layer.msg(data.message, {
                        icon: 1,
                        time: SUCCESS_TIP_TIME,
                        shade: 0.3
                    });
                    setTimeout("getDataList()", SUCCESS_TIP_TIME);
                } else {
                    top.layer.msg(data.message, {
                        icon: 2,
                        time: ERROR_TIP_TIME,
                        shade: 0.3
                    });
                }
            }
        })
    });
}

function delete_image(obj) {
    top.layer.confirm('是否删除该图片？', {
        icon: 3,
        title: '删除确认'
    }, function (index) {
        $(obj).parent().remove();
        top.layer.close(index);
    });
}

function dialog_fun_down(title, url, width, height) {
    layer.confirm(title, function () {
        window.location.href = url;

    });
    return false;
}

function show_video_fun(title, url) {
    var index = top.layer.open({
        type: 2,
        area: ['80%', '65%'],
        fix: false, //不固定
        maxmin: true,
        shade: 0.4,
        shadeClose: false,
        title: title,
        content: url,
        end: function () {
            getDataList();
        }
    });
    layer.full(index);
}

function check_login() {
    login_url = $("#login-form").attr('action')
    query = $("#login-form").serialize();
    $("#login-form button[type='submit']").addClass('disabled').prop('disabled', true);
    $.ajax({
        type: 'post',
        url: login_url,
        data: query,
        dataType: 'json',
        error: function () {
            top.layer.msg('访问失败', {
                icon: 2,
                time: ERROR_TIP_TIME,
                shade: 0.3
            });
        },
        complete: function () {
            $("#login-form button[type='submit']").removeClass('disabled').prop('disabled', false);
        },
        success: function (result) {

            if (result.error !== true) {
                top.layer.msg(result.message, {
                    icon: 1,
                    time: SUCCESS_TIP_TIME,
                    shade: 0.3
                });
                setTimeout(function () {
                    top.window.location.href = result.main_url;
                }, SUCCESS_TIP_TIME);

            } else {
                top.layer.msg(result.message, {
                    icon: 5,
                    time: ERROR_TIP_TIME,
                    shade: 0.3
                });
                if(result.refresh){
                    //刷新验证码
                    $("#captcha").click();
                }
            }
        }
    })
}

function auth_login_fun(title, url, text) {
    text = text == undefined ? '是否操作？' : text;
    top.layer.confirm(text, {
        icon: 3,
        title: title
    }, function (index) {
        top.layer.close(index)
        window.open(url);
    });
}

function report_region_select() {
    ids = $("#region_id_value").val()
    var index = layer.open({
        id: 'dialog_fun',
        type: 2,
        area: ['80%', '65%'],
        fix: false, //不固定
        maxmin: false,
        shade: 0.4,
        shadeClose: false,
        title: '',
        content: '/region/select_area?level=5&more=0&callback=region_id&ids=' + ids,
        end: function () {

        }
    })
}
