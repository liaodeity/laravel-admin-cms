@if(isset($title) && $title)
<title>{{$title ?? ''}}</title>
@endif
@yield('style')
@yield('content')
@yield('footer')
<script type="text/javascript" src="{{mix_build_dist('js/manifest.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/vendor.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/app.js')}}"></script>
