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
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">基本信息</h3>

                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body">
                    <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? ''}}"
                          onsubmit="return false;">
                        @method($method ?? '')
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>配置名称</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputName" value="{{$config->title ?? ''}}"
                                       disabled placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>配置值</label>

                            <div class="col-sm-10">
                                @if($config->name == 'wx_menu')
                                    @foreach($context as $key=> $item)
                                        <div class="row margin-top-5">
                                            <div class="col-xs-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><b>菜单{{$key+1}}</b></span>
                                                    <input type="text" class="form-control" maxlength="5" name="menu[{{$key}}][name]" value="{{$item['name'] ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-xs-4">
                                                <input type="text" class="form-control" maxlength="200" name="menu[{{$key}}][url]" value="{{$item['url'] ?? ''}}">
                                            </div>
                                            <span style="line-height: 30px;">提示：如果子菜单不为空，设置值无效</span>
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
                                                    <input type="text" class="form-control" maxlength="200" name="menu[{{$key}}][sub_button][{{$key2}}][url]" value="{{$item2['url'] ?? ''}}">
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                    <input type="hidden" name="Configs[context]" value="{{$config->context ?? ''}}">
                                @elseif($config->type == 'array')
                                    <select name="Configs[context]" id="" class="form-control">
                                        @foreach($context as $item)
                                            <option value="{{$item->key}}" @if(isset($config->context) && $config->context == $item->key) selected @endif>{{$item->value}}</option>
                                        @endforeach
                                    </select>
                                @else
                                <textarea name="Configs[context]" id="" class="form-control" cols="30"
                                          rows="5">{{$config->context ?? ''}}</textarea>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>配置说明</label>

                            <div class="col-sm-10">

                                <textarea id="" maxlength="200" readonly class="form-control"
                                          @if(!is_super_admin()) disabled @endif  cols="30" rows="5">{{$config->desc ?? ''}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info width-100 margin-top-15" data-confirm="">保
                                    存
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')

@endsection
