@extends('common.page_layouts')
@section('body_class','login-page')
@section('style')

@endsection

@section('content')
    <div class="login-box">
        <div class="login-logo">
            {{get_config_value('agent_sys_title','代理商管理平台')}}登录
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
