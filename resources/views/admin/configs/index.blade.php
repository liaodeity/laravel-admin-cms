@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                系统配置
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">系统管理</a></li>
                <li class="active">系统配置</li>
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
                                    <label for="exampleInputName2">配置名称</label>
                                    <input type="text" name="title" class="form-control" id="exampleInputName2"
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName2">配置值</label>
                                    <input type="text" name="context" class="form-control" id="exampleInputName2"
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

                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="title" class="sorting_desc">配置名称</th>
                                    <th data-field="context" class="sorting_desc">配置值</th>
                                    <th data-field="updated_at" class="sorting">最后修改时间</th>
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
<script>
    function wx_send_fun(){

    }
</script>
@endsection
