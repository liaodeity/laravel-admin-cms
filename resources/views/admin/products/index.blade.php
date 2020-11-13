@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                商品管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">商品管理</li>
            </ol>
        </section>
        <style>
        </style>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <form class="form-inline list-search-form active" action="" onsubmit="return false;">
                                <div class="form-group">
                                    <label for="">商品名称</label>
                                    <input type="text" name="title" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">是否发展会员</label>
                                    <select name="is_develop_member" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($product->isDevelopMemberItem() as $ind => $item)
                                            <option value="{{$ind}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($product->statusItem() as $ind => $item)
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
                            @if(check_admin_permission ('delete products'))
                                <button type="button"
                                        onclick="delete_patch_fun('批量删除','{{route('products.destroy',0)}}','确认是否删除？')"
                                        class="btn btn-sm btn-default">批量删除
                                </button>
                            @endif
                            @if(check_admin_permission ('disable products'))
                                <button type="button"
                                        onclick="confirm_patch_fun('批量下架','{{route ('products.disable',0)}}','确认是否下架？')"
                                        class="btn btn-sm btn-default">批量下架
                                </button>
                            @endif
                            @if(check_admin_permission ('enable products'))
                                <button type="button"
                                        onclick="confirm_patch_fun('批量发布','{{route('products.enable',0)}}','确认是否发布？')"
                                        class="btn btn-sm btn-default">批量发布
                                </button>
                            @endif
                            @if(check_admin_permission ('create products'))
                                <button type="button" onclick="dialog_fun('添加商品信息','{{route('products.create')}}')"
                                        class="btn btn-sm btn-info pull-right"><i
                                        class="fa fa-plus"></i> 添加
                                </button>
                            @endif
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered text-center dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="title" class="sorting">商品分类</th>
                                    <th data-field="title" class="sorting">商品名称</th>
                                    <th data-field="model" class="sorting">型号</th>
                                    <th data-field="specification" class="">规格</th>
                                    <th data-field="price" class="">单价</th>
                                    <th data-field="standard_no" class="sorting">标准</th>
                                    <th data-field="card_background" class="sorting">卡片底色</th>
                                    <th data-field="is_develop_member" class="sorting">是否发展会员</th>
                                    <th data-field="status" class="sorting">状态</th>
                                    <th class="" style="width: 220px">操作</th>
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
