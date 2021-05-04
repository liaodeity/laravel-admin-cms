@extends('layouts.app')
@section('body_class','bg-write')
@section('style')

@endsection

@section('content')
    <div class="layui-container">
        <div class="layui-row">
            <table class="layui-table zhihe-show">
                <colgroup>
                    <col width="20%">
                    <col width="">
                    <col>
                </colgroup>
                <thead>
                </thead>
                <tbody>
                @include('admin.user.base_show')
                <tr>
                    <th>状态</th>
                    <td>{{\App\Libs\Parameter::userStatusItem ($user->member->status ?? '') }}</td>
                </tr>
                <tr>
                    <th>登录次数</th>
                    <td>{{$user->member->login_count ?? '' }}</td>
                </tr>
                <tr>
                    <th>登录次数</th>
                    <td>{{$user->member->last_login_at ?? '' }}</td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td>{{$user->created_at ?? ''}}</td>
                </tr>
                <tr>
                    <th>更新时间</th>
                    <td>{{$user->updated_at ?? ''}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('footer')

@endsection
