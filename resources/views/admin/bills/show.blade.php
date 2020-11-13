@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs ">
                    <li class="active"><a href="#info" data-toggle="tab" aria-expanded="true">佣金信息</a></li>
                    <li class=""><a href="#user" data-toggle="tab" aria-expanded="">获取人信息</a></li>
                </ul>
                <div class="tab-content ">
                    <div class="tab-pane active" id="info">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:20%">佣金编号:</th>
                                <td>{{$bill->bill_no}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">二维码编号:</th>
                                <td>{{$bill->qrcode->qrcode_no ?? ''}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">代理商名称:</th>
                                <td><a class="btn btn-link" onclick="view_fun('查看代理商信息','{{route('agents.show',$bill->agent_id)}}')" >{{$bill->agent->agent_name ?? ''}}</a></td>
                            </tr>
                            <tr>
                                <th style="width:20%">所属商品:</th>
                                <td>{{$bill->product->title}}</td>
                            </tr>
                            <tr>
                                <th>获得佣金:</th>
                                <td>{{$bill->amount}}</td>
                            </tr>
                            <tr>
                                <th>获得时间:</th>
                                <td>{{$bill->bill_at}}</td>
                            </tr>
                            <tr>
                                <th>扫码地址:</th>
                                <td>{{$bill->scan_address}}（{{$bill->lat}},{{$bill->lng}}）</td>
                            </tr>
                            <tr>
                                <th>确认时间:</th>
                                <td>{{$bill->verity_at}}</td>
                            </tr>
                            <tr>
                                <th>状态:</th>
                                <td>{{$bill->statusItem($bill->status)}}</td>
                            </tr>
                            <tr>
                                <th>修改时间:</th>
                                <td>{{$bill->updated_at}}</td>
                            </tr>
                            <tr>
                                <th>创建时间:</th>
                                <td>{{$bill->created_at}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane " id="user">
                        <table class="table">
                            <tbody>
                                @include('admin.members._member_item')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- /.row -->
        </section>


    </div>

@endsection

@section('footer')

@endsection
