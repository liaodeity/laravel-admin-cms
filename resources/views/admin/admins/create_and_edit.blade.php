@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">基本信息</h3>

                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body">
                    <form id="form-iframe-add" data-pjax class="form-horizontal" action="{{$action_url ?? ''}}" onsubmit="return false;">
                        @method($method ?? '')
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>用户名</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Admin[username]" maxlength="20" id="" value="{{$admin->username ?? ''}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>密码</label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="Admin[password]" maxlength="32" id="" placeholder="">
                                <span>（如不修改密码，可为空）</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>管理员名称</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Admin[nickname]" maxlength="20" id="" value="{{$admin->nickname  ?? ''}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>联系电话</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Admin[phone]" id="" onkeyup="value=keyupPhoneTel(this.value)" maxlength="20" value="{{$admin->phone ?? ''}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>所属角色</label>

                            <div class="col-sm-10">
                                <div class="checkbox">
                                    @foreach($roleList as $item)
                                    <label> <input type="checkbox" name="RoleName[]" @if(isset($item->checked) && $item->checked)checked="checked"@endif value="{{$item->name}}"> {{$item->title}} </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($admin->statusItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="Admin[status]" @if(isset($admin->status) && $admin->status == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info width-100 margin-top-15" data-confirm="">保
                                    存
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')

@endsection
