@if(request ()->input ('_pjax'))
    @include('common.pajax_layouts')
@else
    @include('common.admin_layouts')
@endif
