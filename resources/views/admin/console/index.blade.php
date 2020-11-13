@extends('common.layouts')
@section('style')

@endsection
@section('body_class','lockscreen')
@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                工作台
                <small></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">工作台</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{$order_count??0}}<sup style="font-size: 20px">件</sup></h3>

                            <p>订单数量</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{route ('orders.index')}}" class="small-box-footer">查看更多 <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{$member_count ?? 0}}<sup style="font-size: 20px">个</sup></h3>

                            <p>会员数量</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{route ('members.index')}}" class="small-box-footer">查看更多 <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{$bill_amount ?? 0}}<sup style="font-size: 20px">元</sup></h3>

                            <p>产生佣金总额</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="{{route ('bills.index')}}" class="small-box-footer">查看更多 <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{$yes_bill_amount ?? 0}}<sup style="font-size: 20px">元</sup></h3>

                            <p>已发放佣金总额</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{route ('bills.index')}}" class="small-box-footer">查看更多 <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- ./col -->

            </div>

            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <div class="col-md-6">


                    <!-- TABLE: LATEST ORDERS -->
                    <div class="box box-info">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div id="main" style="height:400px;"></div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->

                <div class="col-md-6">
                    <!-- PRODUCT LIST -->
                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div id="user_main" style="height:400px;"></div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <!-- Left col -->
                <div class="col-md-6">


                    <!-- TABLE: LATEST ORDERS -->
                    <div class="box box-info">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div id="bill" style="height:400px;"></div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->

                <div class="col-md-6">
                    <!-- PRODUCT LIST -->
                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div id="bill_main" style="height:400px;"></div>
                        </div>
                        <!-- /.box-body -->
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
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init (document.getElementById ('main'));

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: '订购产品统计'
            },
            tooltip: {
                trigger: 'axis'
            },
            xAxis: {
                data: @json($chart['order']['x'] ?? [])
            },
            yAxis: {},
            series: [ {
                name: '订单数量',
                type: 'bar',
                data: @json($chart['order']['y_num'] ?? [])
            }, {
                name: '订购金额',
                type: 'bar',
                data: @json($chart['order']['y_amount'] ?? [])
            } ]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption (option);

        option = {
            title: {
                text: '月度产生佣金统计'
            },
            tooltip: {
                trigger: 'axis'
            },
            xAxis: {
                type: 'category',
                data: @json($chart['bill']['x'] ?? [])
            },
            yAxis: {
                type: 'value'
            },
            series: [ {
                name: '产生佣金数',
                data: @json($chart['bill']['y'] ?? []),
                type: 'bar'
            } ]
        };
        var myChart = echarts.init (document.getElementById ('bill'))
        myChart.setOption (option);

        var myChart = echarts.init (document.getElementById ('user_main'));

        // 用户
        var option = {
            title: {
                text: '月度新增会员统计'
            },
            tooltip: {},
            legend: {
                data: [ '总销量' ]
            },
            xAxis: {
                data: @json($chart['member']['x'] ?? [])
            },
            yAxis: {},
            series: [ {
                name: '新会员数',
                type: 'bar',
                data: @json($chart['member']['y'] ?? [])
            } ]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption (option);

        var myChart = echarts.init (document.getElementById ('bill_main'));

        // 发放佣金
        var option = {
            title: {
                text: '月度发放佣金统计'
            },
            tooltip: {},
            legend: {
                data: [ '总销量' ]
            },
            xAxis: {
                data: @json($chart['no_bill']['x'] ?? [])
            },
            yAxis: {},
            series: [ {
                name: '发放佣金数',
                type: 'bar',
                data: @json($chart['no_bill']['y'] ?? [])
            } ]
        };
        myChart.setOption (option);
    </script>
@endsection
