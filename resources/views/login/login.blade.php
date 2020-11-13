<!-- /.login-logo -->
<div class="login-box-body">
    <p class="login-box-msg">输入账号密码进行登录</p>

    <form id="login-form" action="{{route('login-check')}}" method="post" onsubmit="return false;">
        <input type="hidden" name="Login[loginType]" value="{{$loginType ?? ''}}">
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="Login[username]" value="" maxlength="32" autocomplete="off" placeholder="账号">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" class="form-control" name="Login[password]" value="" placeholder="密码" maxlength="32" autocomplete="off">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <img id="captcha" src="{{route ('admin-login-captcha')}}" alt="" style="float: left;width: 40%;height: 34px;cursor: pointer;">
            <input type="text" class="form-control" name="Login[captcha]" value="" placeholder="验证码" maxlength="5" autocomplete="off" style="width: 60%;display: inline-block;">
            <span class="fa  fa-check-square-o form-control-feedback"></span>

        </div>
        <div class="row">
            <!-- /.col -->
            <div class="col-xs-12">
                <button type="submit" onclick="check_login()" class="btn btn-primary btn-block btn-flat">登录</button>
            </div>
            <!-- /.col -->
        </div>
    </form>
    <!-- /.social-auth-links -->
</div>
<!-- /.login-box-body -->
