@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                售后管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">订单管理</a></li>
                <li class="active">售后管理</li>
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
                                    <label for="">售后编号</label>
                                    <input type="text" name="sale_no" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">订单编号</label>
                                    <input type="text" name="order_no" class="form-control" value="{{request('order_no','')}}" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">代理商名称</label>
                                    <input type="text" name="agent_name" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">代理商归属名称</label>
                                    <input type="text" name="company_name" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">售后时间</label>
                                    {!! html_date_input('apply_sale_at') !!}
                                </div>
                                <div class="form-group">
                                    <label for="">售后状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($orderSale->statusItem() as $ind => $item)
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
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="sale_no" class="sorting">售后编号</th>
                                    <th data-field="order_no" class="sorting">订单编号</th>
                                    <th data-field="order_sales.agent_id" class="sorting">代理商名称</th>
                                    <th data-field="orders.created_at" class="sorting">下单时间</th>
                                    <th data-field="order_sales.apply_sale_at"  class="sorting">申请售后时间</th>
                                    <th data-field="sale_amount" class="sorting">售后总金额</th>
                                    <th data-field="order_sales.status" class="sorting">售后状态</th>
                                    <th class="" style="width: 250px">操作</th>
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
