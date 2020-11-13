@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="box-body">
                <div class="box box-primary">
                    <div class="table">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:20%">代理商编号:</th>
                                <td>{{$agent->agent_no}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">用户名:</th>
                                <td>{{$agent->username}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">代理商名称:</th>
                                <td>{{$agent->agent_name}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">代理商生日:</th>
                                <td>{{$agent->birthday}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">微信昵称:</th>
                                <td>{{$agent->wx_name}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">代理商归属名称:</th>
                                <td>{{$agent->company_name}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">代理区域:</th>
                                <td>
                                    @foreach($proxyRegion as $region)
                                        <p>{{$region['region_pid_name']}}：{{$region['region_name_str']}}</p>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>联系人:</th>
                                <td>{{$agent->contact_name}}</td>
                            </tr>
                            <tr>
                                <th>联系电话:</th>
                                <td>{{$agent->contact_phone}}</td>
                            </tr>
                            <tr>
                                <th>所在地区：</th>
                                <td>{{$agent->officeRegion->area_region_name ?? ''}}</td>
                            </tr>
                            <tr>
                                <th>详细地址：</th>
                                <td>{{$agent->office_address}}</td>
                            </tr>
                            <tr>
                                <th>直接下级人数:</th>
                                <td>{{$agent->directChildNumber()}}</td>
                            </tr>
                            <tr>
                                <th>间接下级人数:</th>
                                <td>{{$agent->directChildNumber()}}</td>
                            </tr>
                            <tr>
                                <th>授权时长:</th>
                                <td>{{$agent->authorize_date}}@if($agent->is_forever_authorize == 1)长期@endif</td>
                            </tr>

                            <tr>
                                <th>加盟日期:</th>
                                <td>{{$agent->join_date}}</td>
                            </tr>
                            <tr>
                                <th>状态:</th>
                                <td>{{$agent->statusItem($agent->status)}}</td>
                            </tr>
                            <tr>
                                <th>修改时间:</th>
                                <td>{{$agent->updated_at}}</td>
                            </tr>
                            <tr>
                                <th>创建时间:</th>
                                <td>{{$agent->created_at}}</td>
                            </tr>
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
