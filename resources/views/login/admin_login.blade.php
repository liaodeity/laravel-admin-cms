@extends('common.layouts')
@section('body_class','login-page')
@section('style')

@endsection

@section('content')
    <div class="login-box">
        <div class="login-logo">
            后台管理平台登录
        </div>
        @include('login.login')
    </div>
    <!-- /.login-box -->
@endsection

@section('footer')
<script>
    $(function () {
        $("#captcha").click(function () {
            url = '{{ route('admin-login-captcha')}}';
            $("#captcha").attr("src",url+'?t='+Math.random());
        });
    });
</script>
@endsection
