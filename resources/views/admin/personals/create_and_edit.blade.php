@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content-header">
            <h1>
                修改资料
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">其他设置</a></li>
                <li class="active">修改资料</li>
            </ol>
        </section>
        <section class="content">

            <div class="row">
                <div class="col-md-3">
                    @include('admin.personals._nav')
                </div>
                <div class="col-md-9">
                    <div class="box box-primary">
                        <div class="box-body">
                            <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? ''}}" onsubmit="return false;">
                                @method($method ?? '')
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"> <span class="text-red">*</span>用户名</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="" value="{{$admin->username}}" maxlength="20" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span class="text-red">*</span>管理员名称</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="" placeholder="" name="Admin[nickname]" maxlength="20" value="{{$admin->nickname}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span class="text-red">*</span>联系电话</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="" placeholder="" name="Admin[phone]" maxlength="20" onkeyup="value=keyupPhoneTel(this.value)" value="{{$admin->phone}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label"><span
                                            class="text-red"></span>是否接收新订单通知</label>
                                    <div class="col-sm-2">
                                        <div class="checkbox">
                                            <label><input
                                                    type="checkbox" name="Admin[send_order_tips]" value="1" @if(isset($admin->send_order_tips) && $admin->send_order_tips == 1) checked @endif class="">新订单通知</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-info" data-confirm="确认信息正确？">保存</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
@endsection

@section('footer')

@endsection
