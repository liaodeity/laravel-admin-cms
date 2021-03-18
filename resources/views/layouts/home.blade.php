<!DOCTYPE html>
<html lang="zh-CN">
@inject('homeService', "App\Services\HomeService")
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="renderer" content="webkit">
    <title>{{$SEO->title ?? ''}}</title>
    <meta name="keywords" content="{{$SEO->keyword ?? ''}}" />
    <meta name="description" content="{{$SEO->description ?? ''}}" />
    <!-- Bootstrap -->
    <link href="{{asset ('static/bootstrap/3.3.7/css/bootstrap.css')}}" rel="stylesheet">

    <!-- HTML5 shim 和 Respond.js 是为了让 IE8 支持 HTML5 元素和媒体查询（media queries）功能 -->
    <!-- 警告：通过 file:// 协议（就是直接将 html 页面拖拽到浏览器中）访问页面时 Respond.js 不起作用 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link href="{{asset ('css/carousel.css')}}" rel="stylesheet">
    <link href="{{asset ('css/iconfont.website.css')}}" rel="stylesheet">
    <link href="{{asset ('fonts/iconfont.css')}}" rel="stylesheet">
    <link href="{{asset ('css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset ('css/common.css')}}" rel="stylesheet">
    @yield('style')
</head>

<body>
<!--顶部导航：开始-->
<nav class="navbar navbar-white navbar-fixed-top top-hight">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false"
                    aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{url ('/')}}">
                <img alt="Logo" src="{{asset ('images/logo.png')}}">
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                @foreach($homeService->menu() as $menu)
                    @if(empty($menu->_child))
                    <li class="">
                        <a href="{{$menu->_url}}" @if($menu->target) target="{{$menu->target}}" @endif>{{$menu->title}}</a>
                    </li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                {{$menu->title}}<span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @foreach($menu->_child as $menu2)
                                <li>
                                    <a href="{{$menu2->_url}}" @if($menu2->target) target="{{$menu2->target}}" @endif>{{$menu2->title}}</a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</nav>
<!--顶部导航：结束-->
@yield('content')
<!--底部内容：开始-->
<footer>
    <div class="container">
        <div class="row">
            <!--联系我们-->
            <div class="col-md-6">
                <h4 class="text-color3">联系我们</h4>
                <h4 class="text-color6"><i class="icon-website icon-kefu text-orange"></i>&nbsp;24小时贴心客服热线：</h4>
                <h4 class="text-orange text-indent">{{get_config_value ('telephone')}}</h4>
                <h4 class="text-color6"><i class="icon-website icon-address text-orange"></i>&nbsp;联系地址：</h4>
                <h4 class="text-color6 text-indent">{{get_config_value ('address')}}</h4>
            </div>
            <!--二维码区域qrcode-area-->
            <div class="col-md-6 qrcode-area border-color">
                <div class="col-md-4 col-sm-4 text-center">
                    <h4>微信公众号</h4>
                    <img src="{{asset ('images/img-qrcode-wx.png')}}" class="border-color" title="微信扫一扫立即关注">
                    <p>扫描关注</p>
                </div>
                <div class="col-md-4 col-sm-4 text-center">
                    <h4>微信版爱心铃</h4>
                    <img src="{{asset ('images/img-qrcode-wxaxl.png')}}" class="border-color" title="微信扫描关注登录">
                    <p>扫描关注登录</p>
                </div>
                <div class="col-md-4 col-sm-4 text-center">
                    <h4>爱心铃下载</h4>
                    <img src="{{asset ('images/img-qrcode-app.png')}}" class="border-color" title="手机扫一扫立即下载">
                    <p>爱心铃APP</p>
                </div>
            </div>
        </div>
        <hr class="featurette-divider border-color">
        <div class="row copyright text-center text-color8">
            <p>运营支撑：{!! get_config_value ('copyright') !!}</p>
            <p>{!! get_config_value ('icp') !!}</p>
        </div>
    </div>
</footer>
<!--底部内容：结束-->
<!--右下角悬浮固定按钮：开始-->
<div class="buttons-fixed">
    <a href="{{url('axl')}}#download-axl" role="button" class="btn btn-block"><i class="icon-website icon-download"></i><p>APP下载</p></a>
    <a href="{{url('axl')}}#bzrx" role="button" class="btn btn-block"><i class="iconfont iconicon-test"></i><p>报装热线</p></a>
    <a href="{{url('joinus')}}#join_page" role="button" class="btn btn-block "><i class="icon-website icon-join"></i><p>加入颐老</p></a>
    <a href="{{url('feedback')}}" role="button" class="btn btn-block"><i class="icon-website icon-fqa"></i><p>问题反馈</p></a>
    <a id="scrollUp" href="#top" role="button" class="btn btn-block"><i class="icon-website icon-top"></i><p>返回顶部</p></a>
</div>
<!--右下角悬浮固定按钮：结束-->
<!-- jQuery (Bootstrap 的所有 JavaScript 插件都依赖 jQuery，所以必须放在前边) -->
<script src="{{asset ('static/jquery/1.12.4/jquery.min.js')}}"></script>
<!-- 加载 Bootstrap 的所有 JavaScript 插件。你也可以根据需要只加载单个插件。 -->
<script src="{{asset('static/bootstrap/3.3.7/js/bootstrap.min.js')}}"></script>
<script src="{{asset ('static/layer/layer.js')}}"></script>
<script src="{{asset ('js/holder.min.js')}}"></script>
<!--淡入动画-->
<script src="{{asset ('js/wow.min.js')}}"></script>
<script src="{{asset ('js/custom-scripts.js')}}"></script>
<script type="text/javascript" src="{{asset ('js/theia-sticky-sidebar.min.js')}}"></script>
<script src="{{asset ('js/jquery.waypoints.min.js')}}"></script>
@yield('footer')
<script type="text/javascript">
    $(function () {
        $.ajax({
            type: 'post',
            url: '{{url('/view_browsing')}}',
            data: '_token={{csrf_token ()}}',
            success: function () {

            }
        })
    });
</script>
</body>
</html>
