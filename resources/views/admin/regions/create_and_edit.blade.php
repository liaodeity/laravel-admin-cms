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
                    <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? ''}}"
                          onsubmit="return false;">
                        @method($method ?? '')
                        <input type="hidden" name="Region[pid]" value="{{$region->pid ?? 0}}">
                        <input type="hidden" name="Region[area_region]" value="">
                        <input type="hidden" name="Region[province]" value="{{$region->province ?? 0}}">
                        <input type="hidden" name="Region[city]" value="{{$region->city ?? 0}}">
                        <input type="hidden" name="Region[county]" value="{{$region->county ?? 0}}">
                        <input type="hidden" name="Region[town]" value="{{$region->town ?? 0}}">
                        <input type="hidden" name="Region[community]" value="{{$region->community ?? 0}}">
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>区域级别</label>
                            <div class="col-sm-10">

                                    @if($method =='PUT')
                                    <div class="input-group radio">{{$region->levelItem($region->level)}}</div>
                                        @else
                                    <div class="input-group">
                                    <div class="radio">
                                        @foreach($region->levelItem() as $ind => $item)
                                            @if($ind > $region->level || $ind === $region->_province_level)
                                                <label><input name="Region[level]" @if(isset($region->level) && $region->level == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                    </div>
                                        @endif

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>区域名称</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Region[name]" maxlength="50"
                                       value="{{$region->name ?? ''}}" id="" placeholder="">
                            </div>
                        </div>
                        @if($region->pid_name)
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>上级区域名称</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id=""
                                       value="{{$region->pid_name ?? ''}}" disabled="disabled" placeholder="">
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($region->statusItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="Region[status]" @if(isset($region->status) && $region->status == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info width-100 margin-top-15" data-confirm="">
                                    保
                                    存
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

@endsection
