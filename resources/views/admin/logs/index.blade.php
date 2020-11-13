@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                日志管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">其他设置</a></li>
                <li class="active">日志管理</li>
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
                                    <label for="">日志类型</label>
                                    <select name="type" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($logs->typeItem() as $ind => $item)
                                            <option value="{{$ind}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">日志说明</label>
                                    <input type="text" name="content" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">操作时间</label>
                                    {!! html_date_input ('created_at') !!}
                                </div>
                                <div class="form-group">
                                    <label for="">操作人</label>
                                    <input type="text" name="name" class="form-control" id=""
                                           autocomplete="off" placeholder="">
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
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th data-field="type" class="sorting_desc">日志类型</th>
                                    <th data-field="content" class="sorting" style="max-width: 500;">日志说明</th>
                                    <th data-field="" class="">操作人</th>
                                    <th data-field="created_at" class="sorting">操作时间</th>
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
    <script>
        $ (function () {
            getDataList ();
        })
        function showDetail(url) {
            var index = layer.open({
                id: 'dialog_fun',
                type: 2,
                area: ['60%', '60%'],
                fix: false, //不固定
                maxmin: false,
                shade: 0.4,
                shadeClose: true,
                title: '',
                content: url,
                end: function () {

                }
            });
        }
    </script>
@endsection
