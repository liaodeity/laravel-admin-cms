@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                管理员列表
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">账号管理</li>
                <li class="active">管理员列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="">
                <div class="">
                    <div class="box">
                        <div class="box-body">
                            <form data-pjax class="form-inline list-search-form active" action="{{route('admins.index')}}" onsubmit="return false;">
                                <div class="form-group">
                                    <label for="exampleInputName2">管理员名称</label>
                                    <input type="text" name="keyword" class="form-control" value="{{request ()->input ('keyword')}}"
                                           autocomplete="off" placeholder="用户名、名称">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName2">所属角色</label>
                                    <select name="role_id" id="" class="form-control">
                                        <option value=""></option>
                                        @foreach($roleList as $role)
                                        <option @if(request ()->input ('role_id') == $role->id) selected @endif value="{{$role->id}}">{{$role->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName2">状态</label>
                                    <select name="status" class="form-control select2">
                                        <option value=""></option>
                                        @foreach($admin->statusItem() as $ind => $item)
                                            <option @if(request ()->input ('status') == $ind) selected @endif value="{{$ind}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info" data-toggle="modal"
                                            data-target="#modal-success">
                                        搜索
                                    </button>
                                <a type="reset" class="btn btn-default" href="{{route('admins.index')}}">重置</a>
                                </div>
                            </form>

                        </div>
                        <div class="box-body">
                            @if(check_admin_permission('delete admins','删除'))
                                <button type="button"
                                        onclick="delete_patch_fun('批量删除','{{route('admins.destroy',0)}}','确认是否删除？')"
                                        class="btn btn-sm btn-default">批量删除
                                </button>
                            @endif
                            @if(check_admin_permission('disable admins'))
                                <button type="button" onclick="confirm_patch_fun('批量禁用','{{route('admins.disable',0)}}','确认是否禁用？')"
                                        class="btn btn-sm btn-default">批量禁用
                                </button>
                            @endif
                            @if(check_admin_permission('enable admins'))
                                <button type="button" onclick="confirm_patch_fun('批量启用','{{route('admins.enable',0)}}','确认是否启用？')"
                                        class="btn btn-sm btn-default">批量启用
                                </button>
                            @endif
                            @if(check_admin_permission('create admins'))
                                <a href="{{route('admins.create')}}"
                                        class="btn btn-sm btn-info pull-right"><i
                                        class="fa fa-plus"></i> 添加
                                </a>
                            @endif
                        </div>
                        <div class="box-body">
                            <table class="table  table-hover dataTable list-data-table active">
                                <thead>
                                <tr>
                                    <th style="width: 10px"><input class="check-all" type="checkbox"></th>
                                    <th data-field="username" class="sorting">用户名</th>
                                    <th data-field="nickname" class="sorting">管理员名称</th>
                                    <th>所属角色</th>
                                    <th data-field="created_at" class="sorting">添加时间</th>
                                    <th data-field="status" class="sorting">状态</th>
                                    <th class="" style="width: 120px">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                {!! $html ?? '' !!}
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
            // getDataList ();
        })
    </script>
@endsection
