@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <form class="form-inline list-search-form active" action="" onsubmit="return false;">

                                <div class="form-group">
                                    <label for="">注册时间</label>
                                    {!! html_date_input('reg_date') !!}
                                </div>
                                <div class="form-group">
                                    <label for="">名称</label>
                                    <input type="text" name="keyword" class="form-control" id=""
                                           autocomplete="off" placeholder="姓名、微信昵称、微信账号">
                                </div>

                                <div class="form-group">
                                    <label for="">手机号码</label>
                                    <input type="text" name="mobile" value="{{request('mobile')}}" class="form-control" id=""
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">从业年限</label>
                                    <select name="working_year" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($member->workingYearArray() as $item)
                                            <option value="{{$item}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($member->statusItem() as $ind=>$item)
                                            <option value="{{$ind}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info" data-toggle="modal"
                                            data-target="#modal-success">
                                        搜索
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="member_no" class="sorting">会员编号</th>
                                    <th data-field="real_name" class="sorting">姓名</th>
                                    <th data-field="wx_name" class="sorting">微信昵称</th>
                                    <th data-field="mobile" class="sorting">手机号码</th>
{{--                                    <th data-field="progress" class="sorting">待提现佣金</th>--}}
{{--                                    <th data-field="progress" class="sorting">推荐人</th>--}}
{{--                                    <th data-field="progress" class="sorting">抽佣比例</th>--}}
{{--                                    <th data-field="progress" class="sorting">直接下线人数</th>--}}
{{--                                    <th data-field="progress" class="sorting">间接下线人数</th>--}}
                                    <th data-field="reg_date" class="sorting">注册日期</th>
                                    <th data-field="status" class="sorting">状态</th>
                                    <th class="" style="width: 50px">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>


                        </div>
                        <!-- /.box-body -->
                        @include('common.page')
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->


        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')
    <script type="text/javascript">
        $ (function () {
            getDataList ();
            // var index = parent.layer.getFrameIndex ('dialog_fun'); //先得到当前iframe层的索引
            // console.log (index);
            // iframe = parent.frames['layui-layer-iframe'+index];
            // console.log (iframe.select_item_callback());

        })
        function select_item (agent_id, id, name) {

            var index = parent.layer.getFrameIndex (window.name); //先得到当前iframe层的索引
            parent.referrer_user_callback(agent_id, id, name)
            parent.layer.close (index); //再执行关闭
        }
    </script>
@endsection
