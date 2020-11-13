@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->

        <section class="content-header">
            <h1>
                微信回复
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">微信回复</li>
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
                                    <label for="">关键字</label>
                                    <input type="text" name="keyword" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">回复内容</label>
                                    <input type="text" name="content" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($wxReply->statusItem() as $ind => $item)
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
                            <button type="button" onclick="dialog_fun('添加回复信息','{{route('wxReplies.create')}}')"
                                    class="btn btn-sm btn-info pull-right"><i
                                    class="fa fa-plus"></i> 添加
                            </button>

                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>

                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th>关键字</th>
                                    <th data-field="is_subscribe" class="sorting">关注订阅</th>
                                    <th data-field="if_like" class="sorting">匹配类型</th>
                                    <th data-field="content" class="sorting">回复内容</th>
                                    <th data-field="updated_at" class="sorting">更新时间</th>
                                    <th data-field="status" class="sorting">状态</th>
                                    <th class="" style="width: 150px">操作</th>
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
            <script type="text/javascript">
              $ (function () {
                getDataList ();
              })
            </script>

        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')

@endsection
