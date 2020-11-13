@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#info" data-toggle="tab" aria-expanded="true">会员基本信息</a></li>
                    @foreach($member->agents as $agent)
                        <li><a href="#agent{{$agent->id}}" data-toggle="tab">所属【{{$agent->agent->agent_name ?? ''}}】信息</a></li>
                    @endforeach
                </ul>

                <div class="box-body">
                    <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? ''}}" onsubmit="return false;">
                        @method($method ?? '')
                        <div class="tab-content ">
                            <div class="tab-pane active" id="info">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"> <span
                                            class="text-red">*</span>真实姓名</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="Member[real_name]" maxlength="20" value="{{$member->real_name}}" id="" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"> <span
                                            class="text-red"></span>微信号</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="Member[wx_account]" value="{{$member->wx_account}}" maxlength="20" id="" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span class="text-red"></span>二维码名片</label>
                                    <div class="col-sm-6">
                                        <div class="input-group ">
                                            <input type="hidden" id="wx_qr_code" name="Member[wx_qr_code]" value="{{$member->wx_qr_code ?? 0}}">
                                            <input id="wx_qr_code_url" type="text" autocomplete="off" name="" maxlength="220" value="{{show_picture_to_id ($member->wx_qr_code ?? 0) }}" class="form-control width-100 -white">
                                            <span class="input-group-btn">
                                                <button id="filePicker" type="button" class="btn btn-default btn-flat">上传图片</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red">*</span>手机号码</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="Member[mobile]" maxlength="20" onkeyup="value=keyupMobile(this.value)" value="{{$member->mobile}}" id="" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red">*</span>出生日期</label>

                                    <div class="col-sm-2">
                                        <input type="text" class="form-control Wdate-bg" name="Member[birthday]" readonly value="{{$member->birthday}}" id="" placeholder="" onclick="WdatePicker()">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red">*</span>籍贯区域</label>

                                    <div class="col-sm-10">
                                        <input id="native_region_id_value" type="hidden" name="Member[native_region_id]" value="{{$member->native_region_id ?? 0}}">
                                        <input type="text" class="form-control" id="native_region_id_text" readonly value="{{$member->nativeRegion->area_region_name ?? ''}}" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red">*</span>常驻区域</label>

                                    <div class="col-sm-10">
                                        <input id="resident_region_id_value" type="hidden" name="Member[resident_region_id]" value="{{$member->resident_region_id ?? 0}}">
                                        <input type="text" class="form-control" id="resident_region_id_text" readonly value="{{$member->residentRegion->area_region_name ?? ''}}" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red">*</span>常驻住址</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="Member[resident_address]" maxlength="100" value="{{$member->resident_address}}" id="" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red">*</span>工种</label>

                                    <div class="col-sm-10">
                                        <select name="Member[work_type]" id="" class="form-control">
                                            @foreach($member->workTypeArray() as $item)
                                                <option value="{{$item}}" @if(isset($member->work_type) && $member->work_type == $item) selected @endif>{{$item}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red">*</span>从业年限</label>

                                    <div class="col-sm-10">
                                        <select name="Member[working_year]" id="" class="form-control">
                                            @foreach($member->workingYearArray() as $item)
                                                <option value="{{$item}}" @if(isset($member->working_year) && $member->working_year == $item) selected @endif>{{$item}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red"></span>业务渠道</label>

                                    <div class="col-sm-10">
                                        <textarea name="Member[business_channel]" id="" maxlength="500" class="form-control" cols="30" rows="5">{{$member->business_channel}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red">*</span>状态</label>

                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="radio">
                                                @foreach($member->statusItem() as $ind => $item)
                                                    @if($ind >= 0)
                                                        <label><input name="Member[status]" @if(isset($member->status) && $member->status == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @foreach($member->agents as $agent)
                                <div class="tab-pane " id="agent{{$agent->id}}">
                                    <div class="form-group">
                                        <label for="" class="col-sm-2 control-label"><span
                                                class="text-red"></span>推荐人</label>

                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input type="hidden" name="MemberAgent[{{$agent->id}}][agent_id]" value="{{$agent->agent_id}}">
                                                <input type="hidden" id="referrer_member_id{{$agent->agent_id}}" name="MemberAgent[{{$agent->id}}][referrer_member_id]"
                                                       value="{{$agent->referrer_member_id ?? 0}}">
                                                <input type="text" id="referrer_name{{$agent->agent_id}}" class="form-control" id="" readonly name="pid_user_name"
                                                       value="{{$agent->referrer->real_name ?? ''}}"
                                                       placeholder="">
                                                <span class="input-group-btn">
                      <button type="button" onclick="referrer_user({{$agent->agent_id}})" class="btn btn-info btn-flat">选择推荐人</button>
                    </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-sm-2 control-label"><span
                                                class="text-red"></span></label>
                                        <div class="col-sm-10">
                                            <div class="checkbox">
                                                <label><input type="checkbox" name="MemberAgent[{{$agent->id}}][is_allow_subordinate]" value="1" @if(isset($agent->is_allow_subordinate) && $agent->is_allow_subordinate == 1) checked @endif placeholder="">是否可以发展下线</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-sm-2 control-label"><span
                                                class="text-red"></span>抽取佣金比例</label>

                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input type="number" name="MemberAgent[{{$agent->id}}][bill_rate]" min="0" max="100" onkeyup="value=keyupNumber(this.value,2, 100)" class="form-control" value="{{$agent->bill_rate ?? 0}}"
                                                       id=""
                                                       placeholder="">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                            <span>（{{trans('上级对这个会员的抽佣比例，剩余的比例佣金由该会员获取，取值0到100。')}}）</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info width-100 margin-top-15"
                                        data-confirm="{{trans_text ('确认保存？')}}">{{trans_text ('保存')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('footer')
<script type="text/javascript">
    function referrer_user_callback(agent_id, id, name){
        $("#referrer_member_id"+agent_id).val(id)
        $("#referrer_name"+agent_id).val(name)

    }
    function referrer_user(agent_id) {
        id = $("#referrer_member_id"+agent_id).val()
        title = "选择推荐人"
        url  ="{{route('admin.dialogs.referrer')}}?agent_id="+agent_id+'&id='+id+'&no_id={{$member->id ?? 0}}'
        var index = layer.open({
                id: 'dialog_fun',
                type: 2,
                area: ['80%', '65%'],
                fix: false, //不固定
                maxmin: false,
                shade: 0.4,
                shadeClose: false,
                title: title,
                content: url,
                end: function () {

                }
            });
    }
    function select_area_callback (region_str,text_str, callback) {
        $("#" + callback + "_value").val(region_str)
        $("#" + callback + "_text").val(text_str)
    }
    $(function () {
        $("#wx_qr_code_url").change(function () {
            if ($("#wx_qr_code_url").val() == '') {
                $("#wx_qr_code").val(0);
            }
        })
        $("#native_region_id_text").click(function () {
            ids = $("#native_region_id_value").val()
            var index = layer.open ({
                id: 'dialog_fun',
                type: 2,
                area: [ '80%', '65%' ],
                fix: false, //不固定
                maxmin: false,
                shade: 0.4,
                shadeClose: false,
                title: '',
                content: '{{url('region/select_area')}}?level=5&more=0&callback=native_region_id&ids='+ids,
                end: function () {

                }
            });
        })
        $("#resident_region_id_text").click(function () {
            ids = $("#resident_region_id_value").val()
            var index = layer.open ({
                id: 'dialog_fun',
                type: 2,
                area: [ '80%', '65%' ],
                fix: false, //不固定
                maxmin: false,
                shade: 0.4,
                shadeClose: false,
                title: '',
                content: '{{url('region/select_area')}}?level=5&more=0&callback=resident_region_id&ids='+ids,
                end: function () {

                }
            });
        })
        var UPLOAD_URL = '/upload/image/file';
        var _TOKEN = '{{csrf_token()}}'
        // 初始化Web Uploader
        var uploader = WebUploader.create ({

            // 选完文件后，是否自动上传。
            auto: true,

            // swf文件路径
            swf: '{{asset ('admin-ui/lib/webuploader/Uploader.swf')}}',
            formData: {
                _token: _TOKEN,
                work_id: '0'
            },
            // 文件接收服务端。
            server: UPLOAD_URL,

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#filePicker',
            fileVal: 'file',
            // 只允许选择图片文件。
            accept: {
                title: 'Files',
                extensions: 'png,jpg,jpeg'
            }
        });
        uploader.on ('uploadSuccess', function (file, data) {
            if (data.state == 'SUCCESS') {
                $ ("#wx_qr_code").val (data.id);
                $ ("#wx_qr_code_url").val (data.url);
                top.layer.msg ('上传成功', {icon: 1, time: 2000, shade: 0.3});
            } else {

                top.layer.msg (data.info, {icon: 2, time: 2000, shade: 0.3});
            }
            uploader.reset ();
        });
        uploader.option ('compress', {
            width: 750,
            height: 1000
        });
        layer.photos ({
            photos: '.showImages',
            anim: 5
        });
    })
</script>
@endsection
