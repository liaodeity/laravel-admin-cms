@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                数据库备份
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="#">系统管理</a></li>
                <li class="active">数据库备份</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <form class="form-inline list-search-form active" action="" onsubmit="return false;">
                                <div class="form-group">
                                    <label for="exampleInputName2">备份名称</label>
                                    <input type="text" name="name" class="form-control" id="exampleInputName2"
                                           autocomplete="off" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName2">状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($databaseBackups->statusItem() as $ind => $item)
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
                            <button type="button"
                                    onclick="delete_patch_fun('批量删除','{{route('databaseBackups.destroy','0')}}','确认是否删除，备份压缩包将被删除？')"
                                    class="btn btn-sm btn-danger">批量删除
                            </button>
                            {{--//TODO 备份弹窗修改，确认后立即关闭，并提示--}}
                            <button type="button"
                                    onclick="confirm_db_start_fun('备份数据库','{{route('databaseBackups.store')}}','确认现在备份数据库？')"
                                    class="btn btn-sm btn-info pull-right"><i
                                    class="fa fa-plus"></i> 备份数据库
                            </button>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="name" class="sorting">备份名称</th>
                                    <th data-field="start_at" class="sorting">开始备份时间</th>
                                    <th data-field="end_at" class="sorting">完成备份时间</th>
                                    <th data-field="file_size" class="sorting">数据压缩大小</th>
                                    <th data-field="status" class="sorting">状态</th>
                                    <th class="" style="width: 180px">操作</th>
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
    <script>
        $ (function () {
            getDataList ();
        })
        function confirm_db_start_fun(title, url, text) {
            text = text == undefined ? '是否操作？' : text;
            top.layer.confirm(text, {
                icon: 3,
                title: title
            }, function (index) {
                top.layer.close(index);
                top.layer.msg('开始备份成功，请稍后查看', {
                    icon: 1,
                    time: SUCCESS_TIP_TIME,
                    shade: 0.3
                });
                $.ajax({
                    type: "POST",
                    url: url,
                    data: '',
                    dataType: 'json',
                    beforeSend: function () {
                        // 加载效果
                        setTimeout("getDataList()", 800);
                    },
                    complete:function(){
                    },
                    error: function () {
                        top.layer.msg('网络访问失败', {
                            icon: 2,
                            time: ERROR_TIP_TIME,
                            shade: 0.3
                        });
                    },
                    success: function (data) {
                        if (data.error !== true) {
                            top.layer.msg(data.message, {
                                icon: 1,
                                time: SUCCESS_TIP_TIME,
                                shade: 0.3
                            });
                            setTimeout("getDataList()", SUCCESS_TIP_TIME);
                        } else {
                            top.layer.msg(data.message, {
                                icon: 2,
                                time: ERROR_TIP_TIME,
                                shade: 0.3
                            });
                        }
                    }
                })
            });
        }
        function down_db_fun  (title, url, text) {
            text = text == undefined ? '是否操作？' : text;
            top.layer.confirm (text, {
                icon: 3,
                title: title
            }, function (index) {
                top.layer.close(index)
                $("#jump_blank_url").attr('href', url)
                document.getElementById('jump_blank_url').click()
            });
        }
    </script>
@endsection
