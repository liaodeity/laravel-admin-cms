@extends('common.layouts')
@section('style')

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
                    <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? ''}}" onsubmit="return false;">
                        @method($method ?? '')
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>用户名</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Agent[username]" maxlength="20" value="{{$agent->username}}" id="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>密码</label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="Agent[password]" maxlength="32" value="" id="" placeholder="">
                                <span>（如不修改密码，可为空）</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>代理商名称</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Agent[agent_name]" maxlength="50" value="{{$agent->agent_name}}" id="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>代理商生日</label>
                            <div class="col-sm-2">
                                <input  type="text" class="form-control Wdate-bg" autocomplete="off" name="Agent[birthday]" readonly value="{{$agent->birthday}}" onclick="WdatePicker()">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>代理商归属名称</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Agent[company_name]" maxlength="100" value="{{$agent->company_name}}" id="" placeholder="">
                            </div>
                        </div>
                        <div id="area-div" class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>代理区域</label>
                            <div class="col-sm-6">
                                <button class="btn btn-info" onclick="item_select()"><span
                                        class="fa fa-plus"></span>新增区域
                                </button>
                                @foreach($proxyRegion as $region)
                                <div class="input-group margin-top-5">
                                    <input type="hidden" class="form-control" name="RegionId[]" value="{{$region['region_id_str']}}" placeholder="">
                                    <input type="text" class="form-control" id="" value="{{$region['region_pid_name']}}：{{$region['region_name_str']}}" placeholder="" disabled="">
                                    <span class="input-group-btn">
                                            <button class="btn btn-info" onclick="item_select('{{$region['region_id_str']}}')">修改</button>
                                            <button class="btn btn-danger" onclick="javascript:$(this).parents('.input-group').remove()">删除</button>
                                        </span>
                                </div>
                                @endforeach
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>微信昵称</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="" name="Agent[wx_name]" maxlength="50" value="{{$agent->wx_name}}" readonly
                                       placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>联系人</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="" name="Agent[contact_name]" maxlength="20" value="{{$agent->contact_name}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>联系号码</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="" name="Agent[contact_phone]" maxlength="20" onkeyup="value=keyupPhoneTel(this.value)" value="{{$agent->contact_phone}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>所在地区</label>

                            <div class="col-sm-4">
                                <input id="office_region_value" type="hidden" name="Agent[office_region_id]" value="{{$agent->office_region_id ?? 0}}">
                                <input type="text" class="form-control" id="office_region_text" readonly value="{{$agent->officeRegion->area_region_name ?? ''}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>详细地址</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="" name="Agent[office_address]" maxlength="100" value="{{$agent->office_address}}" placeholder="">
{{--                                <div class="input-group col-sm-6">--}}
{{--                                    <div class="input-group-btn">--}}
{{--                                        <button id="region_area_job_text" type="button"--}}
{{--                                                class="btn btn-default @if($agent->office_region_id == 0) hide @endif">{{str_replace('-','',($agent->officeRegion->area_region_name ?? ''))}}</button>--}}
{{--                                    </div>--}}
                                    <!-- /btn-group -->
{{--                                    <input type="text" class="form-control" id="" name="Agent[office_address]" maxlength="100" value="{{$agent->office_address}}" placeholder="">--}}
{{--                                </div>--}}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>授权编号</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="Agent[authorize_no]" maxlength="50" value="{{$agent->authorize_no}}" id="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>授权时长</label>
                            <div class="col-sm-2">
                                <input  type="text" class="form-control Wdate-bg" name="Agent[authorize_date]" value="{{$agent->authorize_date}}" readonly autocomplete="off" onclick="WdatePicker()">
                            </div>
                            <div class="col-sm-2 checkbox">
                                <label><input
                                        type="checkbox" name="Agent[is_forever_authorize]" value="1" @if(isset($agent->is_forever_authorize) && $agent->is_forever_authorize == 1) checked @endif class="">长期</label>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>加盟日期</label>
                            <div class="col-sm-2">
                                <input  type="text" class="form-control Wdate-bg" name="Agent[join_date]" value="{{$agent->join_date}}" readonly autocomplete="off" onclick="WdatePicker()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($agent->statusItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="Agent[status]" @if(isset($agent->status) && $agent->status == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info width-100 margin-top-15" data-confirm="确认保存？">保
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
    <script>

        function item_select (ids) {
            ids = ids ==undefined ? '' : ids
            var index = layer.open ({
                id: 'dialog_fun',
                type: 2,
                area: [ '80%', '65%' ],
                fix: false, //不固定
                maxmin: false,
                shade: 0.4,
                shadeClose: false,
                title: '',
                content: '{{url('region/select_area')}}?source=agent&agent={{$agent->id ?? 0}}&level=4&more=1&ids='+ids,
                end: function () {

                }
            });
        }

        function select_area_callback (region_str,text_str, callback) {
            if(callback == 'office_region'){
                $("#"+callback+"_value").val(region_str)
                $("#"+callback+"_text").val(text_str)
                _text_str = text_str.replace(/-/g,'')
                $("#region_area_job_text").html(_text_str).removeClass('hide')

            }else{
                $("input[name='RegionId[]'][value='"+region_str+"']:first").parents('.input-group').remove()
                $ ("#area-div .col-sm-6").append ('<div class="input-group margin-top-5">\n' +
                    '                                <input type="hidden" class="form-control" name="RegionId[]"  value="'+region_str+'" placeholder="" >\n' +
                    '                                <input type="text" class="form-control" id=""  value="'+text_str+'" placeholder="" disabled>\n' +
                    '                                <span class="input-group-btn">\n' + '' +
                    '<button class="btn btn-info" onclick="item_select(\''+region_str+'\')">修改</button>' +
                    '                                  <button class="btn btn-danger" onclick="javascript:$(this).parents(\'.input-group\').remove()">删除</button>\n' +
                    '                                </span>\n' +
                    '                            </div>')
            }

        }
        $(function () {
            $("#office_region_text").click(function () {
                ids = $("#office_region_value").val()
                var index = layer.open ({
                    id: 'dialog_fun',
                    type: 2,
                    area: [ '80%', '65%' ],
                    fix: false, //不固定
                    maxmin: false,
                    shade: 0.4,
                    shadeClose: false,
                    title: '',
                    content: '{{url('region/select_area')}}?level=5&more=0&callback=office_region&ids='+ids,
                    end: function () {

                    }
                });
            })
        })
    </script>
@endsection
