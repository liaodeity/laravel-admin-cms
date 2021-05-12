@if(isset($title) && $title)
    <title>{{$title ?? ''}}</title>
@endif
<script type="text/javascript">
    MODULE_NAME = '{{$MODULE_NAME ?? ''}}';
    SUCCESS_TIME = 800;
    FAIL_TIME = 2000;
    TABLE_RESIZE_TIME = 3500;
    AJAX_ERROR_TIP = '访问失败';
</script>
@yield('style')
@yield('content')
@yield('footer')
