@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#order" data-toggle="tab" aria-expanded="true">售后信息</a></li>
                    <li><a href="#status_time" data-toggle="tab">售后订单追踪</a></li>
                </ul>

                <div class="tab-content ">
                    <div class="tab-pane active" id="order">
                        <div class="table">
                            <table class="table text-center table-bordered no-margin">
                                <thead>
                                <tr>
                                    <th>商品名称</th>
                                    <th>标准</th>
                                    <th>规格</th>
                                    <th>单价</th>
                                    <th>售后数量</th>
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
                                            <td class="border-right-width-1px">{{$product->price}}</td>
                                            <td>{{$product->orderSaleProduct($orderSale->id)->number ?? 0}}</td>
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
                            <table class="table no-border-top">
                                <tbody>
                                <tr>
                                    <th style="width:20%">售后编号:</th>
                                    <td>{{$orderSale->sale_no}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%">订单编号:</th>
                                    <td>{{$orderSale->order->order_no ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%">代理商名称:</th>
                                    <td>
                                        @if(check_admin_permission('show agents'))
                                        <a class="btn btn-link" onclick="dialog_fun('查看代理商信息','{{route('agents.show',$orderSale->agent->id ?? 0)}}')" >{{$orderSale->agent->agent_name ?? ''}}</a>
                                            @else
                                            {{$orderSale->agent->agent_name ?? ''}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>申请售后时间:</th>
                                    <td>{{$orderSale->apply_sale_at}}</td>
                                </tr>
                                <tr>
                                    <th>快递公司:</th>
                                    <td>{{$orderSale->saleDelivery->name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>快递单号:</th>
                                    <td>{{$orderSale->saleDelivery->delivery_no ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>申请说明:</th>
                                    <td>{{$orderSale->apply_desc}}</td>
                                </tr>
                                <tr>
                                    <th>处理说明:</th>
                                    <td>{{$orderSale->process_desc}}</td>
                                </tr>
                                <tr>
                                    <th>回寄快递公司:</th>
                                    <td>{{$orderSale->sendBackDelivery->name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>回寄快递单号:</th>
                                    <td>{{$orderSale->sendBackDelivery->delivery_no ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>售后状态:</th>
                                    <td>{{$orderSale->statusItem($orderSale->status)}}</td>
                                </tr>
                                <tr>
                                    <th>修改时间:</th>
                                    <td>{{$orderSale->updated_at}}</td>
                                </tr>
                                <tr>
                                    <th>创建时间:</th>
                                    <td>{{$orderSale->created_at}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane " id="status_time">
                        <!-- The timeline -->
                        @if($orderSale->logs->count())
                            <ul class="timeline timeline-inverse">
                                @foreach($orderSale->logs as $item)
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

                                            <h3 class="timeline-header no-border"><strong>{{get_format_name($item)}}</strong> {{$item->title}}
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
