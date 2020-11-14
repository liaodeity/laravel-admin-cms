<!-- /.login-logo -->
<div class="card-body">
    <p class="login-box-msg">输入账号密码进行登录</p>

    <form id="login-form" action="{{route('login-check')}}" method="post" onsubmit="return false;">
        <input type="hidden" name="Login[loginType]" value="{{$loginType ?? ''}}">
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="Login[username]" value="" maxlength="32" autocomplete="off" placeholder="账号">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <input type="password" class="form-control" name="Login[password]" value="" placeholder="密码" maxlength="32" autocomplete="off">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <div class="input-group-append">
                <img id="captcha" src="{{route ('admin-login-captcha')}}" alt="" style="cursor: pointer;">
            </div>
            <input type="text" class="form-control" name="Login[captcha]" value="" placeholder="验证码" maxlength="5" autocomplete="off">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-check"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <button type="submit" id="admin-check-login" class="btn btn-primary btn-block btn-flat">登录</button>
        </div>
    </form>
    <!-- /.social-auth-links -->
</div>
<!-- /.login-box-body -->
