@if(isset($title) && $title)
<title>{{$title ?? ''}}</title>
@endif
@yield('style')
@yield('content')
@yield('footer')
