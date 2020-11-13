@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="box box-primary">
                <div class="box-body">
                    <form id="form-iframe-add" class="form-horizontal" action="" onsubmit="return false;">
                        @method('PUT')
                        <input type="hidden" id="order_id" value="{{$orderSale->id}}">
                        <table class="table text-center table-bordered no-margin">
                            <thead>
                            <tr>
                                <th>商品名称</th>
                                <th>标准</th>
                                <th>规格</th>
                                <th width="200">生产批号</th>
                                <th>单价</th>
                                <th>订购数量</th>
                                <th>卡片底色</th>
                                <th>是否放卡</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orderSale->order->itemOrderProducts($orderSale->order_id) as $item)
                                @foreach($item->products as $key =>  $product)
                                    <tr>
                                        @if($key === 0)
                                            <td rowspan="{{$item->count}}"
                                                style="vertical-align:middle">{{$product->title}}</td>
                                            <td rowspan="{{$item->count}}"
                                                style="vertical-align:middle">{{$product->standard_no}}</td>

                                        @endif
                                            <td
                                                style="vertical-align:middle">{{$product->specification}}</td>
                                        <td class="border-right-width-1px">
                                            <input type="hidden" name="OrderProduct[{{$product->id ?? 0}}][id]"
                                                   value="{{$product->id ?? 0}}">
                                            <input class="form-control" type="text"
                                                   name="OrderProduct[{{$product->id ?? 0}}][generate_batch]"
                                                   maxlength="50" placeholder="可填写生产批号"
                                                   value="{{$product->generate_batch}}">
                                        </td>
                                        <td class="border-right-width-1px">{{$product->price}}</td>
                                        <td class="border-right-width-1px">{{$product->orderSaleProduct($orderSale->id)->number ?? 0}}</td>
                                        @if($key === 0)
                                            <td rowspan="{{$item->count}}">
                                                <small class="label  width-100"
                                                       style="background: {{$product->product->card_background ?? ''}} !important;">
                                                    &nbsp;
                                                </small>
                                            </td>
                                        @endif
                                            <td>
                                                {{$product->isPutCardItem($product->is_put_card ?? '')}}
                                            </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>


                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>销售地址</label>

                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="hidden" id="proxy_region_id_value" value="">
                                    <input type="text" id="proxy_region_id_text" class="form-control" name="OrderQrcode[region_name]" maxlength="50" id=""
                                           value="{{$orderSale->qrcodes()->first()->region_name ?? ''}}"
                                           placeholder="">
{{--                                    <div id="proxy_region_id" class="input-group-addon" style="cursor: pointer;">--}}
{{--                                        <i class="fa fa-hand-pointer-o"></i>--}}
{{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>质检员</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="" name="OrderQrcode[quality_inspector]" maxlength="20"
                                       value="{{$orderSale->qrcodes()->first()->quality_inspector ?? ''}}"
                                       placeholder="可填写质检员">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>生产日期</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control Wdate-bg" id="" name="OrderQrcode[production_date]" readonly
                                       value="{{$orderSale->qrcodes()->first()->production_date ?? date('Y-m-d')}}"
                                       onclick="WdatePicker()" autocomplete="off" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>订单商品二维码数</label>

                            <div class="col-sm-2">
                                <div class="input-group ">
                                    <input id="all_qrcode" type="text" class="form-control" value="{{$orderSale->getQrCodeNumber() ?? 0}}"
                                           disabled>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>已生成二维码个数</label>

                            <div class="col-sm-2">
                                <input id="yes_qrcode" type="text" class="form-control"
                                       value="{{$orderSale->qrcodeSuccessCount() ?? 0}}" disabled>
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>未生成二维码个数</label>
                            <div class="col-sm-2">
                                <input id="no_qrcode" type="text" class="form-control"
                                       value="{{$orderSale->getQrCodeNumber() - $orderSale->qrcodeSuccessCount() }}" disabled>
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">
                            </label>
                            <div class="col-sm-8">
                                <div class="progress hide">
                                    <div id="progress-qrcode" class="progress-bar progress-bar-striped active"
                                         role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"
                                    >
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button id="add_qrcode" type="button" class="btn btn-info " data-confirm="确认生成二维码？">
                                    <i class="fa fa-refresh"></i> 生成二维码
                                </button>
                                @if(check_admin_permission('down orderQrcodes'))
                                    <button id="down-card-qrcode" type="button" class="btn btn-info yes-code-div"
                                            data-confirm="确认下载合格证？">
                                        <i class="fa fa-download"></i> 下载合格证
                                    </button>
                                    <button id="down-qrcode" type="button" class="btn btn-info yes-code-div" data-confirm="确认下载二维码？">
                                        <i class="fa fa-download"></i> 下载二维码
                                    </button>
                                @endif
                                <a class="btn btn-info yes-code-div" target="_parent"
                                   href="{{$orderSale_look_url}}">
                                    查看订单二维码
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>


    </div>
@endsection

@section('footer')
    <script>
        var IS_PROGRESS_RUN = 0;
        var MAX_PROGRESS_RUN = 0;//成功后执行最大次数
        function qrcode_add() {
            loading = layer.load(2)
            setTimeout(function () {
                layer.close(loading)
                $("#down-qrcode").removeClass('hide')
                $('#yes_qrcode').val(100)
                $('#no_qrcode').val(0)
            }, 2000)
        }

        function get_progress() {
            if(IS_PROGRESS_RUN === 1 || MAX_PROGRESS_RUN >= 100){
                return false;
            }
            $.ajax({
                type: "POST",
                url: '{{$progress_url}}',
                data: '',
                dataType: 'json',
                beforeSend: function () {
                    IS_PROGRESS_RUN = 1;
                },
                error: function () {

                },
                complete:function(){
                    IS_PROGRESS_RUN = 0;
                    setTimeout(function () {
                        get_progress();
                    },1000);
                },
                success: function (data) {
                    if (data.error !== true) {
                        data = data.result;
                        val = data.rate;
                        $("#yes_qrcode").val(data.yes_number)
                        $("#no_qrcode").val(data.no_number)
                        $("#progress-qrcode").attr('aria-valuenow', val)
                        $("#progress-qrcode").css('width', val + '%')
                        if(val > 10){
                            $("#progress-qrcode").text("已生成 " + val + "%")
                        }

                        $(".progress").removeClass('hide')
                        MAX_PROGRESS_RUN =val;
                        if (val >= 100) {
                            $('.yes-code-div').show();
                            val = 100;
                            IS_PROGRESS_RUN = 1;
                            $("#progress-qrcode").removeClass('progress-bar-striped active').addClass('progress-bar-success');
                        }else{
                            $("#progress-qrcode").removeClass('progress-bar-success').addClass('progress-bar-striped active');
                        }
                    }
                }
            })
        }
        $(function () {
            //判断是否已生成
            if($("#yes_qrcode").val() == $("#all_qrcode").val()){
                $(".yes-code-div").show()
            }else{
                $(".yes-code-div").hide()
            }
            $("#add_qrcode").click(function () {
                layer.confirm('确认生成二维码？', function (index) {
                    layer.close(index)
                    query = $("#form-iframe-add").serialize()
                    $.ajax({
                        type: "POST",
                        url: '{{$update_url}}',
                        data: query,
                        dataType: 'json',
                        beforeSend: function () {
                            // 加载效果
                            $(".progress").removeClass('hide')
                            setTimeout(function () {
                                IS_PROGRESS_RUN = 0;
                                MAX_PROGRESS_RUN = 0;
                                $("#progress-qrcode").attr('aria-valuenow', 0)
                                $("#progress-qrcode").css('width', '0%')
                                get_progress();//进度
                            }, 100);

                            $("#add_qrcode").addClass('disabled').prop('disabled', true);
                        },
                        error: function () {
                            top.layer.msg('网络访问失败', {
                                icon: 2,
                                time: ERROR_TIP_TIME,
                                shade: 0.3
                            });
                        },
                        complete:function(){
                            $("#add_qrcode").removeClass('disabled').prop('disabled', false);
                        },
                        success: function (data) {
                            if (data.error !== true) {
                                top.layer.msg(data.message, {
                                    icon: 1,
                                    time: SUCCESS_TIP_TIME,
                                    shade: 0.3
                                });
                            } else {
                                top.layer.msg(data.message, {
                                    icon: 2,
                                    time: ERROR_TIP_TIME,
                                    shade: 0.3
                                });
                            }
                        }
                    })
                })
            });
            $("#down-card-qrcode").click(function () {
                layer.confirm('确认下载合格证？', function (index) {
                    layer.close(index)
                    location.href = '{{$down_card_url}}'
                });
            })
            $("#down-qrcode").click(function () {
                layer.confirm('确认下载纯二维码？', function (index) {
                    layer.close(index)
                    location.href = '{{$down_qr_url}}'
                });
            })

            $('.slider').slider()
            $('#my_color').iColor({'x': 10, 'y': -50});
            $("#add_qrcode").click(function () {
                // layer.confirm('确认生成二维码？',function () {
                //   $('.slider').slider()
                // })
            })

            // setInterval(function () {
            //     var val = $("#progress-qrcode").attr('aria-valuenow');
            //     val++;
            //     if (val > 100) {
            //         $("#progress-qrcode").removeClass('progress-bar-striped active').addClass('progress-bar-success');
            //
            //         return false;
            //     }
            //     $("#progress-qrcode").attr('aria-valuenow', val)
            //     $("#progress-qrcode").css('width', val + '%')
            //     $("#progress-qrcode").text("已生成 " + val + "%")
            // }, 100);

            $("#proxy_region_id").click(function () {
                ids = $("#proxy_region_id_value").val()
                var index = layer.open ({
                    id: 'dialog_fun',
                    type: 2,
                    area: [ '80%', '65%' ],
                    fix: false, //不固定
                    maxmin: false,
                    shade: 0.4,
                    shadeClose: false,
                    title: '',
                    content: '{{url('region/select_area')}}?level=3&more=0&callback=proxy_region_id&ids='+ids,
                    end: function () {

                    }
                });
            })

        })
        function select_area_callback (region_str,text_str, callback) {
            $("#" + callback + "_value").val(region_str)
            $("#" + callback + "_text").val(text_str)
        }
    </script>
@endsection
