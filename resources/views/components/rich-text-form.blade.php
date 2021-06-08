@if(!$js)
    @switch($rich_editor)
        @case('umeditor')
        <textarea id="{{$id ?? ''}}" name="{{$name ?? ''}}" type="text/plain" style="width:100%;height:300px;">{!! $value !!}</textarea>
        @case('wangEditor')
        <div id="{{$id ?? ''}}">
            {!! $value !!}
        </div>
    @endswitch
@else
富文本渲染js
@switch($rich_editor)
    @case('umeditor')
        window.{{$id ?? 'um'}} = UM.getEditor('{{$id ?? ''}}', {
            autoFloatEnabled: false,
            imageUrl: '{!! $url !!}'
        });
    @case('wangEditor')
    var editor = new wangEditor('#{{$id ?? ''}}');
    editor.create();
@endswitch
@endif
