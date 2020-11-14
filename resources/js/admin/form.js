//列表数据获取
$(document).on('submit', 'form[data-pjax]', function(event) {
    $.pjax.submit(event, '#pjax-container')
})

// $(document).on("click",".list-search-form.active button[type='submit']", function () {
//     // getDataList();
//     var query = $(".list-search-form.active").serialize();
//     var url = $(".list-search-form.active").attr('action');
//     var loading = null;
//
//     $.ajax({
//         type: "get",
//         url: url,
//         data: query,
//         dataType: 'json',
//         beforeSend: function () {
//             loading = list_loading();
//         },
//         complete: function () {
//             layer.close(loading);
//         },
//         success: function (data) {
//             layer.close(loading);
//             if (data.error !== true) {
//                 $(".list-data-table.active .check-all").prop('checked', false);
//                 $(".list-data-table.active tbody").html(data.html)
//                 $("#list-page .page-total").text(data.total)
//                 $("#list-page .page-nav").html(data.page)
//                 if(data.html == ''){
//                     //无数据显示
//                     var colspan = $(".list-data-table.active thead tr>th").length;
//                     $(".list-data-table.active tbody").html('<tr><td colspan="'+colspan+'" class="no-data-text">数据不存在</td></tr>')
//                 }
//             }
//         },
//         error: function () {
//             top.layer.msg('网络访问失败', {
//                 icon: 2,
//                 time: ERROR_TIP_TIME,
//                 shade: 0.3
//             });
//         }
//     })
// });


//监控表单提交按钮
$(document).on("click","#form-iframe-add button[type='submit']", function () {
    if ($(this).data('confirm') != '' && $(this).data('confirm') != undefined) {
        //询问提示确认提交
        var confirmText = $(this).data('confirm');
        top.layer.confirm(confirmText, {
            icon: 3,
            title: '确认'
        }, function (index) {
            $("#form-iframe-add button[type='submit']").addClass('disabled').prop('disabled', true);
            var query = $("#form-iframe-add").serialize();
            var url = $("#form-iframe-add").attr('action');
            $.ajax({
                type: "post",
                url: url,
                data: query,
                dataType: 'json',
                beforeSend:function(){
                    loading = list_loading()
                },
                complete: function () {
                    $("#form-iframe-add button[type='submit']").removeClass('disabled').prop(
                        'disabled', false);
                    layer.close(loading)
                },
                error: function () {
                    top.layer.msg('访问失败', {
                        icon: 2,
                        time: ERROR_TIP_TIME,
                        shade: 0.3
                    });
                },
                success: function (data) {
                    if (data.error !== true) {
                        $(document).Toasts('create', {
                            autohide:true,
                            delay: SUCCESS_TIP_TIME,
                            class: 'bg-success min-width-tip',
                            title: '操作成功',
                            body: data.message,
                        });
                        if (data.url) {
                            // location.href = data.url;
                            $.pjax.reload('#pjax-container', {url:data.url})
                        } else {
                            window.history.back()
                        }
                    } else {
                        $(document).Toasts('create', {
                            autohide:true,
                            delay: ERROR_TIP_TIME,
                            class: 'bg-danger min-width-tip',
                            title: '操作失败',
                            body: data.message,
                        });
                    }
                    $("#form-iframe-add button[type='submit']").removeClass('disabled').prop(
                        'disabled', false);
                }
            })
            top.layer.close(index);
        });
        return false;
    }
    $("#form-iframe-add button[type='submit']").addClass('disabled').prop('disabled', true);
    var query = $("#form-iframe-add").serialize();
    var url = $("#form-iframe-add").attr('action');
    $.ajax({
        type: "post",
        url: url,
        data: query,
        dataType: 'json',
        beforeSend:function(){
            loading = list_loading()
        },
        complete: function () {
            $("#form-iframe-add button[type='submit']").removeClass('disabled').prop('disabled', false);
            layer.close(loading)
        },
        error: function () {
            // console.log(2222);
            top.layer.msg('访问失败', {
                icon: 2,
                time: ERROR_TIP_TIME,
                shade: 0.3
            });
        },
        success: function (data) {
            console.log(data);
            if (data.error !== true) {
                $(document).Toasts('create', {
                    autohide:true,
                    autoremove:false,
                    delay: SUCCESS_TIP_TIME,
                    class: 'bg-success min-width-tip',
                    title: '操作成功',
                    body: data.message,
                });
                if (data.url != undefined && data.url != '') {
                    $.pjax.reload('#pjax-container', {url:data.url})
                } else {
                    window.history.back()
                }
            } else {
                $(document).Toasts('create', {
                    autohide:true,
                    delay: ERROR_TIP_TIME,
                    class: 'bg-danger min-width-tip',
                    title: '操作失败',
                    body: data.message,
                });
            }
            $("#form-iframe-add button[type='submit']").removeClass('disabled').prop('disabled', false);
        }
    })
})
