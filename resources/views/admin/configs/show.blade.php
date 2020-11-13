@extends('common.layouts')
@section('style')
    <style>
        .two-menu-left{
            min-width: 61px;
            border: 0;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="box-body">
                <div class="box box-primary">
                    <div class="table">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:20%">配置名称:</th>
                                <td>{{$config->title ?? ''}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">配置内容:</th>
                                <td>
                                    @if($config->name == 'wx_menu')
                                        <p>提示：以下显示为有效菜单内容，为发布后的最终效果</p>
                                        @foreach($context as $key=> $item)
                                            <div class="row margin-top-5">
                                                <div class="col-xs-4">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><b>菜单{{$key+1}}</b></span>
                                                        <input type="text" class="form-control" maxlength="5" name="menu[{{$key}}][name]" value="{{$item['name'] ?? ''}}">
                                                    </div>
                                                </div>
                                                @if(!isset($item['sub_button']) || empty($item['sub_button']))
                                                    <div class="col-xs-8">
                                                        <input type="text" class="form-control" maxlength="20" name="menu[{{$key}}][url]" value="{{$item['url'] ?? ''}}">
                                                    </div>
                                                @endif
                                            </div>
                                            @if(isset($item['sub_button']) && is_array($item['sub_button']))
                                                @foreach($item['sub_button'] as $key2 => $item2)
                                                    <div class="row  margin-top-5  margin-left-half">
                                                        <div class="col-xs-4">
                                                            <div class="input-group">
                                                                <span class="input-group-addon two-menu-left"></span>
                                                                <input type="text" class="form-control" maxlength="5" name="menu[{{$key}}][sub_button][{{$key2}}][name]" value="{{$item2['name'] ?? ''}}">
                                                            </div>

                                                        </div>
                                                        <div class="col-xs-8">
                                                            <input type="text" class="form-control" maxlength="20" name="menu[{{$key}}][sub_button][{{$key2}}][url]" value="{{$item2['url'] ?? ''}}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endforeach
                                        <input type="hidden" name="Configs[context]" value="{{$config->context ?? ''}}">
                                    @else
                                    {{$context ?? ''}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>配置说明:</th>
                                <td>{{$config->desc ?? ''}}</td>
                            </tr>
                            <tr>
                                <th>修改时间:</th>
                                <td>{{$config->updated_at ?? ''}}</td>
                            </tr>
                            <tr>
                                <th>创建时间:</th>
                                <td>{{$config->created_at ?? ''}}</td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <!-- /.row -->
        </section>


    </div>
@endsection

@section('footer')
<script>
    $(function () {
        $("input").attr('disabled','disabled')
    })
</script>
@endsection
