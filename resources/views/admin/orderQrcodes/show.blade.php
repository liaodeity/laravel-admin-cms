@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#info" data-toggle="tab" aria-expanded="true">基本信息</a></li>
                    <li class=""><a href="#use_qr" data-toggle="tab" aria-expanded="">扫码记录</a></li>
                </ul>
                <div class="tab-content ">
                    <div class="tab-pane active" id="info">
                        <div class="table">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th style="width:20%">二维码编号:</th>
                                    <td>{{$orderQrcode->qrcode_no}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%">订单编号:</th>
                                    <td>{{$orderQrcode->order->order_no ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%">订单编号:</th>
                                    <td>{{$orderQrcode->orderSale->order_sale_no ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%">代理商名称:</th>
                                    <td> <a class="btn btn-link" onclick="view_fun('查看代理商信息','{{route('agents.show',$orderQrcode->agent_id)}}')" >{{$orderQrcode->agent->agent_name ?? ''}}</a></td>
                                </tr>
                                <tr>
                                    <th style="width:20%"> 授权区域:</th>
                                    <td> {{$orderQrcode->region->area_region_name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%"> 规格:</th>
                                    <td> {{$orderQrcode->specification}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%">商品价格:</th>
                                    <td> {{$orderQrcode->price}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%">发放佣金:</th>
                                    <td> {{$orderQrcode->brokerage}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%"> 下单时间:</th>
                                    <td> {{$orderQrcode->order->created_at ?? ''}}</td>
                                </tr>

                                <tr>
                                    <th>商品二维码</th>
                                    <td>
                                        @if($qrcode_content)
                                        <p ><img src="data:image/png;base64,{{$qrcode_content ?? ''}}" width="500px" alt="" style="border:1px solid #ccc;"></p>
                                        <a href="{{route('orderQrcodes.down',$orderQrcode->id)}}" target="_blank" class=" btn btn-xs btn-default">下载二维码</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:20%"> 订单状态:</th>
                                    <td> {{$orderQrcode->order->statusItem($orderQrcode->order->status)}}</td>
                                </tr>
                                <tr>
                                    <th>修改时间:</th>
                                    <td>{{$orderQrcode->created_at}}</td>
                                </tr>
                                <tr>
                                    <th>创建时间:</th>
                                    <td>{{$orderQrcode->updated_at}}</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="tab-pane " id="use_qr">
                        @if($orderQrcode->logs->count())
                            <ul class="timeline timeline-inverse">
                                @foreach($orderQrcode->logs as $item)
                                    @if(!isset($log_last_date) || $log_last_date != $item->created_at->format('Y-m-d'))
                                        <li class="time-label">
                                <span class="bg-green">
                                  {{$item->created_at->format('Y-m-d')}}
                                </span>
                                        </li>
                                    @endif
                                    <li>
                                        <i class="fa fa-envelope bg-aqua"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> {{$item->created_at->format('H:i:s')}}</span>

                                            <h3 class="timeline-header no-border"><strong>{{get_format_name($item)}}</strong>
                                                {{$item->title ?? ''}}
                                            </h3>
                                            <div class="timeline-body">
                                                {{$item->content}}
                                            </div>
                                        </div>
                                    </li>
                                    @php
                                        $log_last_date =  $item->created_at->format('Y-m-d');
                                    @endphp
                                @endforeach
                                <li>
                                    <i class="fa fa-clock-o bg-gray"></i>
                                </li>
                            </ul>
                        @else
                            暂无信息
                        @endif
                    </div>
                </div>
            </div>

            <!-- /.row -->
        </section>


    </div>

@endsection

@section('footer')

@endsection
