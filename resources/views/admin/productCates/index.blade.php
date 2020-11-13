@extends('common.layouts')
@section('style')

@endsection

@section('content')

    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                产品分类
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">产品管理</a></li>
                <li class="active">产品分类</li>
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
                                    <label for="exampleInputName2">产品分类</label>
                                    <input type="text" name="cate_name" class="form-control" id="exampleInputName2"
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName2">状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($productCate->statusItem() as $ind => $item)
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
                            @if(check_admin_permission ('delete productCates'))
                            <button type="button" onclick="delete_patch_fun('批量删除','{{route ('productCates.destroy',0)}}','确认是否删除？')"
                                    class="btn btn-sm btn-default">批量删除
                            </button>
                            @endif
                            @if(check_admin_permission ('create productCates'))
                            <button type="button" onclick="dialog_fun('添加产品分类信息','{{route ('productCates.create')}}')"
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
                                    <th data-field="cate_name" class="sorting">分类名称</th>
                                    <th data-field="" class="">发布产品数</th>
                                    <th data-field="created_at" class="sorting">创建时间</th>
                                    <th data-field="status" class="sorting">状态</th>
                                    <th class="" style="width: 180px">操作</th>
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

        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')
    <script type="text/javascript">
        $ (function () {
            getDataList ();
        })
    </script>
@endsection
