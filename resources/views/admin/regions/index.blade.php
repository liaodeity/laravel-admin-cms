@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                行政区域设置
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">系统管理</a></li>
                <li class="active">行政区域设置</li>
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
                                    <label for="">区域等级</label>
                                    <select name="level" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($region->levelItem() as $ind => $item)
                                            <option value="{{$ind}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">区域名称</label>
                                    <input type="text" name="name" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">上级区域名称</label>
                                    <input type="text" name="pidName" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($region->statusItem() as $ind => $item)
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
{{--                            <button type="button" onclick="delete_patch_fun('批量删除','{{route('regions.destroy',0)}}','确认是否删除？')"--}}
{{--                                    class="btn btn-sm btn-danger">批量删除--}}
{{--                            </button>--}}
                            <button type="button" onclick="dialog_fun('添加区域信息','{{route('regions.create')}}')"
                                    class="btn btn-sm btn-info pull-right"><i
                                    class="fa fa-plus"></i> 添加
                            </button>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="level" class="sorting_desc">区域等级</th>
                                    <th data-field="name" class="sorting_desc">区域名称</th>
                                    <th data-field="pid" class="sorting_desc">上级区域名称</th>
                                    <th data-field="created_at" class="sorting_desc">添加时间</th>
                                    <th data-field="status" class="sorting">状态</th>
                                    <th class="" style="width: 180px">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            @include('common.page')
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')
    <script>
        $ (function () {
            getDataList ();
        })
        function searchPidName (id, name) {
            $("input[name='pidName']").val(name)
            getDataList()
        }
    </script>
@endsection
