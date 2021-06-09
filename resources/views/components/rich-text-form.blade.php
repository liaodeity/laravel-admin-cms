@if(!$js)
    @switch($rich_editor)
        @case('umeditor')
        <textarea id="{{$id ?? ''}}" name="{{$name ?? ''}}" type="text/plain" style="width:100%;height:300px;">{!! $value !!}</textarea>
        @break;
        @case('wangEditor')
        <textarea id="{{$id ?? ''}}_value" class="layui-hide" name="{{$name ?? ''}}" style="width:100%;height:300px;">{!! $value !!}</textarea>
        <div id="{{$id ?? ''}}">
            {!! $value !!}
        </div>
        @break;
    @endswitch
@else
//富文本渲染js
@switch($rich_editor)
    @case('umeditor')
        window.{{$id ?? 'um'}} = UM.getEditor('{{$id ?? ''}}', {
            autoFloatEnabled: false,
            imageUrl: '{!! $url !!}'
        });
    @break;
    @case('wangEditor')
    var {{$id ?? 'wang'}} = new wangEditor('#{{$id ?? ''}}');
    {{$id ?? 'wang'}}.config.uploadImgServer = '{!! $url !!}';
    {{$id ?? 'wang'}}.config.uploadFileName = 'upfile';
    {{$id ?? 'wang'}}.config.uploadImgMaxLength = 1;
    {{$id ?? 'wang'}}.config.height = 500;
    {{$id ?? 'wang'}}.config.customAlert = function (info) {
        layer.alert(info);
    };
    {{$id ?? 'wang'}}.config.onchange = function (newHtml) {
        $("#{{$id ?? 'wang'}}_value").text(newHtml)
    }
    {{$id ?? 'wang'}}.create();
    @break;
@endswitch
@endif
