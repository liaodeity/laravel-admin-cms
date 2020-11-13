@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#info" data-toggle="tab" aria-expanded="true">会员基本信息</a></li>
                    @foreach($member->agents as $agent)
                    <li><a href="#agent{{$agent->id}}" data-toggle="tab">所属【{{$agent->agent->agent_name ?? ''}}】信息</a></li>
                    @endforeach
                </ul>
                <div class="tab-content ">
                    <div class="tab-pane active" id="info">
                        <div class="table">
                            <table class="table">
                                <tbody>
                                    @include('admin.members._member_item',['member',$member])
                                </tbody>
                            </table>

                        </div>
                    </div>
                    @foreach($member->agents as $agent)
                    <div class="tab-pane " id="agent{{$agent->id}}">
                        <div class="table">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th>代理商名称:</th>
                                    <td>
                                        @if(check_admin_permission('view agents'))
                                        <a class="btn btn-link" href="{{route('agents.show', $agent->agent_id)}}">{{\App\Entities\Agent::showName($agent->agent_id)}}</a>
                                            @else
                                            {{\App\Entities\Agent::showName($agent->agent_id)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>推荐人:</th>
                                    <td>{{$agent->referrer->real_name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>抽取佣金比例:</th>
                                    <td>{{$agent->bill_rate ?? 0}}%</td>
                                </tr>
                                <tr>
                                    <th>待提现佣金:</th>
                                    <td>{{$agent->noPayBillAmount()}}</td>
                                </tr>
                                <tr>
                                    <th>已提现金额:</th>
                                    <td>{{$agent->yesPayBillAmount()}}</td>
                                </tr>
                                <tr>
                                    <th>注册GPS坐标</th>
                                    <td>{{$agent->loc_address ?? ''}}（{{$agent->lat}},{{$agent->lng}}）</td>
                                </tr>
                                <tr>
                                    <th>注册现场图片</th>
                                    <td>
                                        <div class="layer-photos-preview">
                                            @foreach($agent->pictures as $picture)
                                            <img src="{{show_picture_url($picture)}}" width="120" alt="">
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>审核状态:</th>
                                    <td>{{$member->statusItem($agent->sp_status)}}</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection

@section('footer')

@endsection
