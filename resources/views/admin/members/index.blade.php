@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                会员列表
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">会员管理</a></li>
                <li class="active">会员列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <form class="form-inline list-search-form active" action="" onsubmit="return false;">
                                <div class="form-group">
                                    <label for="">注册时间</label>
                                    {!! html_date_input('reg_date') !!}
                                </div>
                                <div class="form-group">
                                    <label for="">代理商名称</label>
                                    <input type="text" name="agent_name" value="{{request('agent_name','')}}" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">名称</label>
                                    <input type="text" name="keyword" class="form-control" id=""
                                           autocomplete="off" placeholder="姓名、微信昵称、微信账号">
                                </div>

                                <div class="form-group">
                                    <label for="">手机号码</label>
                                    <input type="text" name="mobile" value="{{request('mobile')}}" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">推荐人</label>
                                    <input type="text" name="referrer" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">所属区域</label>
                                    <input id="region_id_value" type="hidden" name="region_id" value="">
                                    <input type="text" id="region_id_text" name="area_region_name" class="form-control cursor-pointer" readonly onclick="report_region_select()"
                                           autocomplete="off" placeholder="">
                                </div>

                                <div class="form-group">
                                    <label for="">从业年限</label>
                                    <select name="working_year" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($member->workingYearArray() as $item)
                                            <option value="{{$item}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($member->statusItem() as $ind=>$item)
                                            <option value="{{$ind}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info" data-toggle="modal"
                                            data-target="#modal-success">
                                        搜索
                                    </button>
                                </div>
                            </form>

                        </div>
                        <div class="box-body">
                            @if(check_admin_permission('delete members'))
                                <button type="button"
                                        onclick="delete_patch_fun('批量删除','{{route('members.destroy',0)}}','确认是否删除？')"
                                        class="btn btn-sm btn-default">批量删除
                                </button>
                            @endif
                            @if(check_admin_permission('disable members'))
                                <button type="button"
                                        onclick="confirm_patch_fun('批量禁用','{{route('members.disable',0)}}','确认是否禁用？')"
                                        class="btn btn-sm btn-default">批量禁用
                                </button>
                            @endif
                            @if(check_admin_permission('enable members'))
                                <button type="button"
                                        onclick="confirm_patch_fun('批量启用','{{route('members.enable',0)}}','确认是否启用？')"
                                        class="btn btn-sm btn-default">批量启用
                                </button>
                            @endif
                            @if(check_admin_permission('export members'))
                                <button type="button"
                                        onclick="confirm_export_fun('导出','{{route('members.export')}}','确认导出当前查询条件数据？')"
                                        class="btn btn-sm btn-default">导出
                                </button>
                            @endif
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="member_no" class="sorting">会员编号</th>
                                    <th data-field="real_name" class="sorting">姓名</th>
                                    <th data-field="wx_name" class="sorting">微信昵称</th>
                                    <th data-field="mobile" class="sorting">手机号码</th>
                                    <th data-field="" class="">待提现佣金</th>
                                    <th data-field="" class="">直接下线人数</th>
                                    <th data-field="" class="">间接下线人数</th>
                                    <th data-field="reg_date" class="sorting">注册日期</th>
                                    <th data-field="status" class="sorting">状态</th>
                                    <th class="" style="width: 120px">操作</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                        <!-- /.box-body -->
                        @include('common.page')
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')
    <script type="text/javascript">
        function select_area_callback(region_str, text_str, callback) {
            $("#" + callback + "_value").val(region_str)
            $("#" + callback + "_text").val(text_str)

        }
        $ (function () {
            getDataList ();
            $("#region_id_text").click(function () {
                ids = $("#region_id_value").val()
                var index = layer.open ({
                    id: 'dialog_fun',
                    type: 2,
                    area: [ '80%', '65%' ],
                    fix: false, //不固定
                    maxmin: false,
                    shade: 0.4,
                    shadeClose: false,
                    title: '',
                    content: '{{url('region/select_area')}}?level=5&more=0&callback=region_id&ids='+ids,
                    end: function () {

                    }
                });
            })
        })
    </script>

@endsection
