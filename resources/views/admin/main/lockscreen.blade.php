@extends('common.layouts')
@section('style')

@endsection
@section('body_class','lockscreen')
@section('content')
    <div class="hold-transition lockscreen">
        <div class="lockscreen-wrapper">
            <div class="lockscreen-logo">
                后台管理平台
            </div>
            <!-- User name -->
            <div class="lockscreen-name">{{$admin->nickname ?? $admin->username}}</div>

            <!-- START LOCK SCREEN ITEM -->
            <div class="lockscreen-item">
                <!-- lockscreen image -->
                <div class="lockscreen-image">
                    <img src="admin-ui/dist/img/user1-128x128.jpg" alt="User Image">
                </div>
                <!-- /.lockscreen-image -->

                <!-- lockscreen credentials (contains the form) -->
                <form id="login-form" class="lockscreen-credentials"  action="{{route('login-check')}}" method="post" onclick="return false;">
                    <input type="hidden" name="Login[loginType]" value="admin">
                    <input type="hidden" name="Login[username]" value="{{$admin->username}}">
                    <div class="input-group">
                        <input type="password" class="form-control" name="Login[password]" value="" maxlength="32" placeholder="password">

                        <div class="input-group-btn">
                            <button type="submit" class="btn" onclick="check_login()"><i class="fa fa-arrow-right text-muted"></i></button>
                        </div>
                    </div>
                </form>
                <!-- /.lockscreen credentials -->

            </div>
            <!-- /.lockscreen-item -->
            <div class="help-block text-center">
                输入你的密码，进行屏幕解锁
            </div>
            <div class="text-center">
                <a href="{{route ('admin-login')}}">使用登录其他账号</a>
            </div>
            <div class="lockscreen-footer text-center">
                Copyright &copy; {{date('Y')}} All rights reserved
            </div>
        </div>
        <!-- /.center -->
    </div>
@endsection

@section('footer')
    <script>
    </script>
@endsection
