@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                已处理佣金
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">佣金管理</a></li>
                <li class="active">已处理佣金</li>
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
                                    <label for="">佣金编号</label>
                                    <input type="text" name="bill_no" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">代理商名称</label>
                                    <input type="text" name="agent_name" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">所属商品</label>
                                    <input type="text" name="title" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">会员信息</label>
                                    <input type="text" name="member_keyword" class="form-control" id=""
                                           autocomplete="off" placeholder="会员名称、编号、手机号码">
                                </div>
                                <div class="form-group">
                                    <label for="">获得时间</label>
                                    {!! html_date_input ('bill_at') !!}
                                </div>
                                <div class="form-group">
                                    <label for="">确认时间</label>
                                    {!! html_date_input ('verity_at') !!}
                                </div>
                                <div class="form-group">
                                    <label for="">佣金状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($bill->statusItem() as $ind=>$item)
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
                                    <th data-field="bill_no" class="sorting">佣金编号</th>
                                    <th data-field="agent_id" class="sorting">代理商名称</th>
                                    <th data-field="product_id" class="sorting">所属商品</th>
                                    <th data-field="member_id" class="sorting">会员编号</th>
                                    <th data-field="members.real_name" class="sorting">会员名称</th>
                                    <th data-field="members.mobile" class="sorting">会员手机号</th>
                                    <th data-field="amount" class="sorting">获得佣金</th>
                                    <th data-field="bill_at" class="sorting">获得时间</th>
                                    <th data-field="scan_address" class="sorting">扫码地址</th>
                                    <th data-field="verity_at" class="sorting">确认时间</th>
                                    <th data-field="status" class="sorting" >佣金状态</th>
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
