@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                代理商管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">代理商管理</li>
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
                                    <label for="">加盟时间</label>
                                    {!! html_date_input ('join_date') !!}
                                </div>

                                <div class="form-group">
                                    <label for="">名称</label>
                                    <input type="text" name="keyword" class="form-control" id=""
                                           autocomplete="off" placeholder="名称、微信昵称、联系人">
                                </div>
                                <div class="form-group">
                                    <label for="">联系电话</label>
                                    <input type="text" name="contact_phone" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">授权区域</label>
                                    <input id="proxy_region_id_value" type="hidden" name="area_region" value="">
                                    <input type="text" class="form-control cursor-pointer" name="area_region_name" id="proxy_region_id_text" readonly value="" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">所在地区</label>
                                    <input type="text" name="address" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>

                                <div class="form-group">
                                    <label for="">状态</label>
                                    <select name="agents.status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($agent->statusItem() as $ind => $item)
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
                            @if(check_admin_permission ('delete agents'))
                                <button type="button" onclick="delete_confirm_fun('批量删除','{{route ('agents.destroy',0)}}','确认是否删除？')"
                                        class="btn btn-sm btn-default">批量删除
                                </button>
                            @endif
                            @if(check_admin_permission ('disable agents'))
                                <button type="button" onclick="confirm_patch_fun('批量禁用','{{route('agents.disable',0)}}','确认是否禁用？')"
                                        class="btn btn-sm btn-default">批量禁用
                                </button>
                            @endif
                            @if(check_admin_permission ('enable agents'))
                                <button type="button" onclick="confirm_patch_fun('批量启用','{{route ('agents.enable',0)}}','确认是否启用？')"
                                        class="btn btn-sm btn-default">批量启用
                                </button>
                            @endif
                            @if(check_admin_permission ('export agents'))
                                <button type="button" onclick="confirm_export_fun('导出','{{route('agents.export')}}','确认导出当前查询条件数据？')"
                                        class="btn btn-sm btn-default">导出
                                </button>
                            @endif
                            @if(check_admin_permission ('create agents'))
                                <button type="button" onclick="dialog_fun('添加代理商信息','{{route('agents.create')}}')"
                                        class="btn btn-sm btn-info pull-right"><i
                                        class="fa fa-plus"></i> 添加
                                </button>
                            @endif
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="agent_no" class="sorting">代理商编号</th>
                                    <th data-field="username" class="sorting">用户名</th>
                                    <th data-field="agent_name" class="sorting">代理商名称</th>
                                    <th data-field="wx_name" class="sorting">微信昵称</th>
                                    <th data-field="progress" class="sorting">联系人</th>
                                    <th data-field="progress" class="sorting">联系电话</th>
                                    <th data-field="progress" class="sorting">办公地址</th>
                                    <th data-field="progress" class="sorting">直接下线人数</th>
                                    <th data-field="progress" class="sorting">间接下线人数</th>
                                    <th class="sorting">加盟日期</th>
                                    <th class="sorting">状态</th>
                                    <th class="" style="width: 240px">操作</th>
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
        function select_area_callback (region_str,text_str, callback) {
                $("#" + callback + "_value").val(region_str)
                $("#" + callback + "_text").val(text_str)

        }
        $(function () {
            getDataList();

            $("#proxy_region_id_text").click(function () {
                ids = $("#proxy_region_id_value").val()
                var index = layer.open ({
                    id: 'dialog_fun',
                    type: 2,
                    area: [ '80%', '65%' ],
                    fix: false, //不固定
                    maxmin: false,
                    shade: 0.4,
                    shadeClose: false,
                    title: '',
                    content: '{{url('region/select_area')}}?level=5&more=0&callback=proxy_region_id&ids='+ids,
                    end: function () {

                    }
                });
            })
        })
    </script>
@endsection
