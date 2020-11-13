@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                会员男女比例佣金统计
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">统计报表</a></li>
                <li class="active">会员男女比例佣金统计</li>
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
                                    <label for="">统计方式</label>
                                    <select name="countType" class="form-control select2">
                                        <option value="day" selected>每日</option>
                                        <option value="month">每月</option>
                                        <option value="season">每季</option>
                                        <option value="year">每年</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">佣金领取时间</label>
                                    {!! html_date_input('bill_at') !!}
                                </div>
                                <div class="form-group">
                                    <label for="">会员性别</label>
                                    <select name="gender" class="form-control select2">
                                        <option value=""></option>
                                        <option value="男" >男</option>
                                        <option value="女" >女</option>
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
                            <button type="button"
                                    onclick="confirm_export_fun('导出','{{route('admin-reports.member-gender-bill','export=1')}}','确认导出当前查询条件数据？')"
                                    class="btn btn-sm btn-default">导出
                            </button>
                        </div>
                        <div class="box-body">

                            <table class="table table-bordered table-hover dataTable list-data-table text-center active">
                                <tbody></tbody>
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
        $ (function () {
            getDataList ();
        })
    </script>
@endsection
