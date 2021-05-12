@if(request ()->input ('_pjax'))
    @include('layouts.pajax_layouts')
@else
    @include('layouts.admin_layouts')
@endif
