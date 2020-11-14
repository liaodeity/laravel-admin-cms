@if(request ()->input ('_pjax'))
    @include('common.pajax_layouts')
@else
    @include('common.page_layouts')
@endif
