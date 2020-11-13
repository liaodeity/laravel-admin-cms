@extends('common.layouts')
@section('style')
    <style type="text/css">
        div.adp-wraper p span {
            width: 19%;
        }
        .ul-div{
            height: 100% !important;
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="adp-wraper" class="adp-wraper">
                        <p class="region-header">
                            <span data-level="1" class="adp-head-active">省份</span><span data-level="2">城市</span>@if($maxLevel>=3)<span data-level="3">县\区</span>@endif @if($maxLevel>=4)<span
                                data-level="4">镇区\街道</span>@endif @if($maxLevel>=5)<span data-level="5">社区</span>@endif
                        </p>
                        <div class="ul-div region-level1" style="display: block; height: 262px;">
                            <ul>
                                @foreach($provinces as $region)
                                    <li data-id="{{$region->id}}" data-title="{{$region->name}}" data-level="{{$region->level}}" data-area="{{$region->area_region_name}}"
                                        class="region-row {{$region->active ?? ''}}">{{$region->name}}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="ul-div region-level2" style="height: 65px; display: none;">
                            <ul>
                                @foreach($cities as $region)
                                    <li data-id="{{$region->id}}" data-title="{{$region->name}}" data-level="{{$region->level}}" data-area="{{$region->area_region_name}}"
                                        class="region-row {{$region->active ?? ''}}">{{$region->name}}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="ul-div region-level3" style="height: 65px; display: none;">
                            <ul>
                                @foreach($counties as $region)
                                    <li data-id="{{$region->id}}" data-title="{{$region->name}}" data-level="{{$region->level}}" data-area="{{$region->area_region_name}}"
                                        class="region-row {{$region->active ?? ''}}">{{$region->name}}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="ul-div region-level4" style="height: 65px; display: none;">
                            <ul>
                                @foreach($towns as $region)
                                    <li data-id="{{$region->id}}" data-title="{{$region->name}}" data-level="{{$region->level}}" data-area="{{$region->area_region_name}}"
                                        class="region-row {{$region->active ?? ''}}">{{$region->name}}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="ul-div region-level5" style="height: 65px; display: none;">
                            <ul>
                                @foreach($communities as $region)
                                    <li data-id="{{$region->id}}" data-title="{{$region->name}}" data-level="{{$region->level}}" data-area="{{$region->area_region_name}}"
                                        class="region-row {{$region->active ?? ''}}">{{$region->name}}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="adp-btn-area">
                            <div id="adp_btn_0" class="adp-btn" onclick="confirm_item()">确认选择</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@section('footer')
    <script type="text/javascript">
        var MAX_LEVEL = '{{$maxLevel}}';
        var IS_MORE = '{{$more}}';
        function confirm_item() {
            var level = $(".region-header .adp-head-active").data('level')
            var select = [];
            var selectName = []
            var areaName = [];
            if ($(".region-level" + level + " .active").length == 0) {
                level--;
                if ($(".region-level" + level + " .active").length == 0) {
                    level--;
                }
                if (level < 1)
                    level = 1;
            }
            console.log(level);

            $(".region-level" + level + " .active").each(function (ind, val) {
                select.push($(this).data('id'))
                selectName.push($(this).data('title'))
                areaName.push($(this).data('area'))
            })
            console.log(select);
            region_str = select.join(',');
            text_str = selectName.join('、')
            console.log(region_str);
            console.log(text_str);
            if(IS_MORE != 1){
                //单选
                text_str = areaName.join('、')
            }
            parent.select_area_callback(region_str, text_str,'{{$callback}}')
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index); //再执行关闭
        }

        function get_region_pid(level, pid) {
            var select = [];
            $(".region-level" + level + " .active").each(function (ind, val) {
                select.push($(this).data('id'))
            })
            select = select.join(',');
            next_level = level + 1;
            $.ajax({
                type: 'post',
                url: '{{url('region/get-region-pid')}}',
                data: 'source={{$source ?? ''}}&{{$source ?? 'source_value'}}={{$source_value ?? ''}}&pid=' + pid + '&level=' + level + '&select=' + select,
                dataType: 'json',
                success: function (data) {
                    if (data.error !== true) {
                        result = data.result;
                        if(result.list.length === 0){
                            return false;
                        }
                        str = '';
                        result.list.forEach(function (val, ind) {
                            str += '<li data-id="' + val.id + '" data-title="' + val.name + '" class="region-row ' + val._active + '" data-level="'+val.level+'" data-area="'+val.area+'">' + val.name + '</li>';
                            if(val.level)
                                next_level = val.level
                        });
                        $(".ul-div").hide()
                        // console.log(str);
                        // console.log(".region-level" + (level + 1) + " ul");
                        $(".region-level" + (next_level) + " ul").html(str)
                        $(".region-level" + (next_level)).show();
                        $(".region-header span").removeClass('adp-head-active')
                        $(".region-header span[data-level='"+next_level+"']").addClass('adp-head-active')
                    } else {

                    }
                }
            })
        }

        $(function () {
            for (var i=1; i<=5;i++){
                if($(".region-level"+i+" li.active").length > 0){
                    $("#adp-wraper .ul-div").hide();
                    $("#adp-wraper .region-level"+i).show();
                    $("#adp-wraper .region-header span").removeClass('adp-head-active');
                    $("#adp-wraper .region-header span[data-level='"+i+"']").addClass('adp-head-active');
                }
            }


            $("#adp-wraper .region-header span").click(function () {
                var level = $(this).data('level')
                $("#adp-wraper .ul-div").hide();
                if(level > MAX_LEVEL){
                    return false;
                }
                $("#adp-wraper .region-level" + level).show();
                // pid = $(".region-level" + (level-1) + " .active").data('id');
                // get_region_pid(level,pid);
                $("#adp-wraper .region-header span").removeClass('adp-head-active');
                $(this).addClass('adp-head-active');
            })
            $("#adp-wraper").on('click', '.ul-div li', function () {
                var level = $(this).data('level')
                // console.log($(this));
                console.log($(this).hasClass('active'));
                // console.log(level);
                var id = $(this).data('id')
                if($(this).hasClass('active')){
                    $(this).removeClass('active')
                }else{

                    if(level < MAX_LEVEL){
                        //获取下级列表
                        $(".region-level" + level +" ul li").removeClass('active');
                        $(this).addClass('active')
                        get_region_pid(level, id);
                    }else{
                        if(IS_MORE != 1){
                            //非多选
                            $(".region-level" + level +" ul li").removeClass('active');
                        }
                        $(this).addClass('active')
                    }
                }

            })
        })
    </script>
@endsection
