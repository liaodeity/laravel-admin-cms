@extends('layouts.app')
@section('style')
    <style>
        .layui-card {
            border: 1px solid #f2f2f2;
            border-radius: 5px;
        }

        .icon {
            margin-right: 10px;
            color: #1aa094;
        }

        .icon-cray {
            color: #ffb800 !important;
        }

        .icon-blue {
            color: #1e9fff !important;
        }

        .icon-tip {
            color: #ff5722 !important;
        }

        .layuimini-qiuck-module {
            text-align: center;
            margin-top: 10px
        }

        .layuimini-qiuck-module a i {
            display: inline-block;
            width: 100%;
            height: 60px;
            line-height: 60px;
            text-align: center;
            border-radius: 2px;
            font-size: 30px;
            background-color: #F8F8F8;
            color: #333;
            transition: all .3s;
            -webkit-transition: all .3s;
        }

        .layuimini-qiuck-module a cite {
            position: relative;
            top: 2px;
            display: block;
            color: #666;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            font-size: 14px;
        }

        .welcome-module {
            width: 100%;
            height: 210px;
        }

        .panel {
            background-color: #fff;
            border: 1px solid transparent;
            border-radius: 3px;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: 0 1px 1px rgba(0, 0, 0, .05)
        }

        .panel-body {
            padding: 10px
        }

        .panel-title {
            margin-top: 0;
            margin-bottom: 0;
            font-size: 12px;
            color: inherit
        }

        .label {
            display: inline;
            padding: .2em .6em .3em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25em;
            margin-top: .3em;
        }

        .layui-red {
            color: red
        }

        .main_btn > p {
            height: 40px;
        }

        .layui-bg-number {
            background-color: #F8F8F8;
        }

        .layuimini-notice:hover {
            background: #f6f6f6;
        }

        .layuimini-notice {
            padding: 7px 16px;
            clear: both;
            font-size: 12px !important;
            cursor: pointer;
            position: relative;
            transition: background 0.2s ease-in-out;
        }

        .layuimini-notice-title, .layuimini-notice-label {
            padding-right: 100px !important;
            text-overflow: ellipsis !important;
            overflow: hidden !important;
            white-space: nowrap !important;
        }

        .layuimini-notice-title {
            line-height: 28px;
            font-size: 14px;
        }

        .layuimini-notice-extra {
            position: absolute;
            top: 50%;
            margin-top: -8px;
            right: 16px;
            display: inline-block;
            height: 16px;
            color: #999;
        }

        /*日志分页*/
        #log-page {
            margin: 0;
            padding: 0;
        }

        #log-page .pagination {
            display: block;
        }

        #log-page .pagination li {
            float: left;
        }
        #log-page .pagination li.active span{
            background-color: #009688;
            color:#fff;
        }
    </style>
@endsection
@section('content')
    <div class="layuimini-container">
        <div class="layuimini-main">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md8">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md6">
                            <div class="layui-card">
                                <div class="layui-card-header"><i class="fa fa-warning icon"></i>数据统计</div>
                                <div class="layui-card-body">
                                    <div class="welcome-module">
                                        <div class="layui-row layui-col-space10">
                                            <div class="layui-col-xs6">
                                                <div class="panel layui-bg-number">
                                                    <div class="panel-body">
                                                        <div class="panel-title">
                                                            <span class="label pull-right layui-bg-blue">实时(<span class="real_second">10</span>s)</span>
                                                            <h5>用户数</h5>
                                                        </div>
                                                        <div class="panel-content">
                                                            <h1 id="sync_real_member" class="no-margins">-</h1>
                                                            <small>截止今天用户的数量</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-col-xs6">
                                                <div class="panel layui-bg-number">
                                                    <div class="panel-body">
                                                        <div class="panel-title">
                                                            <span class="label pull-right layui-bg-green">实时(<span class="real_second">10</span>s)</span>
                                                            <h5>今日订单</h5>
                                                        </div>
                                                        <div class="panel-content">
                                                            <h1 id="sync_real_today_order" class="no-margins">-</h1>
                                                            <small>今天的订单数据</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-col-xs6">
                                                <div class="panel layui-bg-number">
                                                    <div class="panel-body">
                                                        <div class="panel-title">
                                                            <span class="label pull-right layui-bg-cyan">实时(<span class="real_second">10</span>s)</span>
                                                            <h5>本月订单</h5>
                                                        </div>
                                                        <div class="panel-content">
                                                            <h1 id="sync_real_month_order" class="no-margins">-</h1>
                                                            <small>截止今天本月订单数量</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-col-xs6">
                                                <div class="panel layui-bg-number">
                                                    <div class="panel-body">
                                                        <div class="panel-title">
                                                            <span class="label pull-right layui-bg-orange">实时(<span class="real_second">10</span>s)</span>
                                                            <h5>本月订单金额</h5>
                                                        </div>
                                                        <div class="panel-content">
                                                            <h1 id="sync_real_month_order_money" class="no-margins">-</h1>
                                                            <small>截止今天本月订单金额</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md6">
                            <div class="layui-card">
                                <div class="layui-card-header"><i class="fa fa-credit-card icon icon-blue"></i>快捷入口</div>
                                <div class="layui-card-body">
                                    <div class="welcome-module">
                                        <div class="layui-row layui-col-space10 layuimini-qiuck">
                                            @foreach($shortcutList as $key => $item)
                                                @if($key < 8)
                                                    <div class="layui-col-xs3 layuimini-qiuck-module">
                                                        <a href="javascript:;" layuimini-content-href="{{$item->href ?? ''}}?mpi=m-p-i-{{$item->id}}"
                                                           data-title="{{$item->title ?? ''}}"
                                                           data-icon="{{$item->icon ?? 'fa fa-list-alt'}}">
                                                            <i class="{{$item->icon ?? 'fa fa-list-alt'}}"></i>
                                                            <cite>{{$item->title ?? ''}}</cite>
                                                        </a>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="layui-col-md12">
                            <div class="layui-card">
                                <div class="layui-card-header"><i class="fa fa-line-chart icon"></i>报表统计
                                    <div class="f-r">
                                        <div class="layui-input-inline">
                                            <input type="text" value="{{$echart_date ?? ''}}" id="start_date" readonly autocomplete="off"
                                                   class="layui-input"
                                                   style="height: 24px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-card-body">

                                    <div id="echarts-records" style="width: 100%;min-height:470px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="layui-col-md4">

                    <div class="layui-card">
                        <div class="layui-card-header"><i class="fa fa-bullhorn icon icon-tip"></i>待办日志</div>
                        <div id="to_do_log" class="layui-card-body layui-text">

                        </div>
                        <div id="log-page" class="layui-card-body layui-box layui-laypage layui-laypage-default">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer')
    <script>
        var SECOND = 15;
        function get_sync_real_num() {
            $.ajax({
                type: 'post',
                url: '{{url('/admin/main/sync_real_num')}}',
                complete: function () {

                },
                success: function (data) {
                    if (data.code === 0) {
                        $("#sync_real_member").text(data.member);
                        $("#sync_real_today_order").text(data.today_order);
                        $("#sync_real_month_order").text(data.month_order);
                        $('#sync_real_month_order_money').text(data.month_order_money);
                    }
                }
            })
        }

        function set_echart(dates) {
            /**
             * 报表功能
             */
            var echartsRecords = echarts.init(document.getElementById('echarts-records'), 'walden');


            var optionRecords = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['访问人数','浏览次数']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: []
                },
                yAxis: {
                    type: 'value'
                },
                series: []
            };
            if (dates === undefined || !dates) {
                dates = $("#start_date").val();
            }
            $.ajax({
                type: 'post',
                url: '{{url('/admin/main/get_echart')}}',
                data: 'dates=' + dates,
                beforeSend: function () {
                    echartsRecords.showLoading();
                },
                complete: function () {
                    echartsRecords.hideLoading();
                },
                success: function (data) {
                    if (data.code === 0) {
                        optionRecords.xAxis.data = data.x_data;
                        var series = [
                            {
                                name: '访问人数',
                                type: 'line',
                                data: data.series.users
                            },
                            {
                                name: '浏览次数',
                                type: 'line',
                                data: data.series.views
                            }
                        ]
                        optionRecords.series = series;
                        echartsRecords.setOption(optionRecords);

                    }
                }
            })
            // echarts 窗口缩放自适应
            window.onresize = function () {
                echartsRecords.resize();
            }
        }

        layui.use(['layer', 'miniTab', 'echarts', 'laydate'], function () {
            var $ = layui.jquery,
                layer = layui.layer,
                miniTab = layui.miniTab,
                echarts = layui.echarts;
            laydate = layui.laydate;
            miniTab.listen();
            get_sync_real_num();
            $(".real_second").text(SECOND);
            setInterval(function () {
                var num = $(".real_second:first").text();
                num--;
                if(num > 0){
                    $(".real_second").text(num);
                }else{
                    $(".real_second").text(SECOND);
                    get_sync_real_num()
                }
            }, 1000);
            set_echart();
            //日期
            laydate.render({
                elem: '#start_date',
                trigger: 'click' //采用click弹出
                , range: true,
                max: 0,
                done: function (value, date) {
                    set_echart(value);
                }
            });
            /**
             * 查看公告信息
             **/
            $('body').on('click', '.layuimini-notice', function () {
                log_id = $(this).data('id')
                is_read = $(this).data('read')
                if (is_read != 1) {
                    //标记已读
                    $.ajax({
                        type: 'post',
                        url: '{{url('admin/logs/read')}}/' + log_id,
                        data: '',
                        success: function (json) {
                            if (json.code === 0) {
                                $("#log-" + log_id).find('.layuimini-notice-title span').removeClass('layui-bg-orange').addClass('layui-bg-green').text('[已读]');
                                $("#log-" + log_id).data('read', 1);
                            } else {
                                top.layer.msg(json.msg, {
                                    icon: 2,
                                    time: FAIL_TIME,
                                    shade: 0.3
                                });
                            }
                        }
                    });
                }
                if ($(this).data('iframe-tab')) {
                    //已存在条件，无需查看明细
                    return false;
                }
            });


            $("#log-page").on('click', 'a.page-link', function () {
                url = $(this).attr('href');
                get_log(url)
                return false;
            });
            get_log('{{url('admin/main/logs')}}')

        });

        function get_log(url) {
            $.ajax({
                type: 'post',
                url: url,
                data: '',
                beforeSend: function () {
                    $("#to_do_log").html('<p style="text-align: center;"><img src="{{asset ('static/admin/images/loading-2.gif')}}" alt=""></p>');
                    $("#log-page").html('')
                },
                success: function (json) {
                    if (json.code == 0) {
                        str = '';
                        result = json.data;
                        $("#to_do_log").html('没有待办')
                        result.forEach(function (val, ind) {
                            class_name = val.is_read == 1 ? 'layui-bg-green' : 'layui-bg-orange'
                            if (val.url) {
                                str += '<div id="log-' + val.id + '" class="layuimini-notice" data-read="' + val.is_read + '" data-id="' + val.id + '" data-iframe-tab="' + val.url + '" data-title="' + val.title + '" data-icon="fa fa-list-alt" >\n' +
                                    '                                <div class="layuimini-notice-title"><span class="layui-badge ' + class_name + '">[' + val.read + ']</span>' + val.content + '</div>\n' +
                                    '                                <div class="layuimini-notice-extra">' + val.log_at + '</div>\n' +
                                    '                                <div class="layuimini-notice-content layui-hide">\n' +
                                    '                                </div>\n' +
                                    '                            </div>\n';
                            } else {
                                str += '<div id="log-' + val.id + '" class="layuimini-notice" data-read="' + val.is_read + '" data-id="' + val.id + '">\n' +
                                    '                                <div class="layuimini-notice-title"><span class="layui-badge ' + class_name + '">[' + val.read + ']</span>' + val.content + '</div>\n' +
                                    '                                <div class="layuimini-notice-extra">' + val.log_at + '</div>\n' +
                                    '                                <div class="layuimini-notice-content layui-hide">\n' +
                                    '                                </div>\n' +
                                    '                            </div>';
                            }
                            $("#to_do_log").html(str);
                        })
                        $("#log-page").html(json.page)
                    }
                }
            })
        }
    </script>
@endsection
