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
                                <th style="width:20%">用户名:</th>
                                <td> {{$admin->nickname}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">管理员名称:</th>
                                <td> {{$admin->nickname}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">联系电话:</th>
                                <td> {{$admin->phone}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">所属角色:</th>
                                <td> {{$role_name}}</td>
                            </tr>
                            <tr>
                                <th>状态:</th>
                                <td>{!! $admin->statusItem($admin->status, false) !!}</td>
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

            <!-- /.row -->
        </section>


    </div>
@endsection

@section('footer')

@endsection
