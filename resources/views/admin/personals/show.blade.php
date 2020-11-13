@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content-header">
            <h1>
                账号管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">账号管理</li>
            </ol>
        </section>
        <section class="content">

            <div class="row">
                <div class="col-md-3">
                    @include('admin.personals._nav')
                </div>
                <div class="col-md-9">
                    <div class="box box-primary">
                        <div class="table">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th style="width:20%">用户名:</th>
                                    <td>{{$admin->username}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%">管理员名称:</th>
                                    <td>{{$admin->nickname}}</td>
                                </tr>
                                <tr>
                                    <th>联系电话:</th>
                                    <td>{{$admin->phone}}</td>
                                </tr>
                                <tr>
                                    <th>加盟日期:</th>
                                    <td>{{$admin->created_at->format('Y-m-d')}}</td>
                                </tr>
                                <tr>
                                    <th>是否接收新订单通知:</th>
                                    <td>@if($admin->send_order_tips == 1) 是 @else 否 @endif</td>
                                </tr>
                                <tr>
                                    <th>修改时间:</th>
                                    <td>{{$admin->updated_at}}</td>
                                </tr>
                                <tr>
                                    <th>创建时间:</th>
                                    <td>{{$admin->created_at}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>

@endsection

@section('footer')
@endsection
