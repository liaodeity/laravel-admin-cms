@extends('common.layouts')
@section('style')

@endsection

@section('content')
<div class="main-content">
    <section class="content-header">
        <h1>
            修改密码
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
            <li><a href="#">其他设置</a></li>
            <li class="active">修改密码</li>
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
                                    <input type="text" class="form-control"  value="{{$admin->nickname}}" placeholder="" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label"><span class="text-red">*</span>旧密码</label>

                                <div class="col-sm-10">
                                    <input type="password" class="form-control"  name="Password[old]" maxlength="32" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label"><span class="text-red">*</span>新密码</label>

                                <div class="col-sm-10">
                                    <input type="password" class="form-control"  name="Password[new]" maxlength="32" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label"><span class="text-red">*</span>确认新密码</label>

                                <div class="col-sm-10">
                                    <input type="password" class="form-control"  name="Password[new2]" maxlength="32" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-info" data-confirm="确认修改密码？">修改密码</button>
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
