$("#admin-check-login").click(function () {
    login_url = $("#login-form").attr('action')
    query = $("#login-form").serialize();
    $("#login-form button[type='submit']").addClass('disabled').prop('disabled', true);
    axios.post(login_url, query).then((response) => {
        $("#login-form button[type='submit']").removeClass('disabled').prop('disabled', false);
        result = response.data;
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
            if (result.refresh) {
                //刷新验证码
                $("#captcha").click();
            }
        }
    }).catch((error) => {
        top.layer.msg('访问失败', {
            icon: 2,
            time: ERROR_TIP_TIME,
            shade: 0.3
        });
    });
    // axios.post({
    //     type: 'post',
    //     url: login_url,
    //     data: query,
    //     dataType: 'json',
    //     error: function () {
    //
    //     },
    //     complete: function () {
    //
    //     },
    //     success: function (result) {
    //
    //
    //     }
    // })
})


