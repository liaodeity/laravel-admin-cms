@extends('common.layouts')
@section('body_class','login-page')
@section('style')

@endsection

@section('content')
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                后台管理平台登录
            </div>
            @include('login.login')
        </div>
        <!-- /.login-box -->
    </div>
@endsection

@section('footer')
    <script>
        $(function () {
            $("#captcha").click(function () {
                url = '{{ route('admin-login-captcha')}}';
                $("#captcha").attr("src", url + '?t=' + Math.random());
            });
        });
    </script>
@endsection
