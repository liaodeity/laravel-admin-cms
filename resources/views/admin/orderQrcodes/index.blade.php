@extends('common.layouts')
@section('style')

@endsection

@section('content')

    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                产品二维码
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">订单管理</a></li>
                <li class="active">产品二维码</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <form class="form-inline list-search-form active" action="{{route('orderQrcodes.index')}}" onsubmit="return false;">
                                <div class="form-group">
                                    <label for="">二维码编号</label>
                                    <input type="text" name="qrcode_no" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">订单编号</label>
                                    <input type="text" name="order_no" class="form-control" id="" value="{{request()->order_no ?? ''}}"
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">售后订单编号</label>
                                    <input type="text" name="sale_no" class="form-control" id="" value="{{request()->sale_no ?? ''}}"
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
                                    <label for="">商品名称</label>
                                    <input type="text" name="title" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">下单时间</label>
                                    {!! html_date_input ('created_at') !!}
                                </div>
                                <div class="form-group">
                                    <label for="">订单状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($order->statusItem() as $ind => $item)
                                            @if($order->isShowQrcode($ind))
                                            <option value="{{$ind}}">{{$item}}</option>
                                            @endif
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
                            <button type="button" onclick="confirm_patch_fun('批量下载','del.php?status=-1','确认是否下载当前查询条件的二维码？')"
                                    class="btn btn-sm btn-default">批量下载
                            </button>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="qrcode_no" class="sorting_desc">二维码编号</th>
                                    <th data-field="orders.order_no" class="sorting_desc">订单编号</th>
                                    <th data-field="agent_name" class="sorting">代理商名称</th>
                                    <th data-field="products.title" class="sorting">商品名称</th>
                                    <th data-field="specification" class="sorting">规格</th>
                                    <th data-field="price" class="sorting">商品价格</th>
                                    <th data-field="brokerage" class="sorting">发放佣金</th>
                                    <th data-field="orders.created_at" class="sorting">下单时间</th>
                                    <th data-field="orders.status" class="sorting">订单状态</th>
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
        function down_qrcode(title, url, text) {
            text = text == undefined ? '是否操作？' : text;
            top.layer.confirm (text, {
                icon: 3,
                title: title
            }, function (index) {
                top.layer.close(index)
                location.href  = url;
            });
        }
    </script>
@endsection
