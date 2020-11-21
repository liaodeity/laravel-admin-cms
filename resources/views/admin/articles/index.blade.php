@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">公告列表</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin">首页</a></li>
                        <li class="breadcrumb-item ">通知公告</li>
                        <li class="breadcrumb-item active">公告列表</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
    </div>

    <!-- Main content -->
    <section id="vue-app" class="content">
        <article-list></article-list>
{{--        <div class="card">--}}
{{--            <div class="card-header">--}}
{{--                <div class="float-right">--}}
{{--                    <a href="{{route('articles.create')}}"--}}
{{--                       class="btn btn-sm btn-info pull-right"><i--}}
{{--                            class="fa fa-plus"></i> 添加--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--                <div class="float-left">--}}
{{--                    <button id="search-choice" type="button" class="btn btn-info btn-sm ">--}}
{{--                        <i class="fa fa-filter"></i><span class="hidden-xs">&nbsp;&nbsp;筛选</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div id="filter-body" class="card-body hidden">--}}
{{--                <form class="form-horizontal list-search-form active" action="" onsubmit="return false;">--}}
{{--                    <div class="card-body row">--}}
{{--                        <div class="form-group row col-sm-6">--}}
{{--                            <label class="col-sm-2 col-form-label text-right">公告标题</label>--}}
{{--                            <div class="col-sm-8 input-group ">--}}
{{--                                <div class="input-group-prepend">--}}
{{--                                    <span class="input-group-text"><i class="fas fa-edit"></i></span>--}}
{{--                                </div>--}}
{{--                                <input type="text" name="title" class="form-control" id=""--}}
{{--                                       autocomplete="off" placeholder="">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group row col-sm-6">--}}
{{--                            <label class="col-sm-2 col-form-label text-right">发布来源</label>--}}
{{--                            <div class="col-sm-8 input-group ">--}}
{{--                                <div class="input-group-prepend">--}}
{{--                                    <span class="input-group-text"><i class="fas fa-edit"></i></span>--}}
{{--                                </div>--}}
{{--                                <input type="text" name="push_source" class="form-control" id=""--}}
{{--                                       autocomplete="off" placeholder="">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group row col-sm-6">--}}
{{--                            <label class="col-sm-2 col-form-label text-right">发布时间</label>--}}
{{--                            <div class="col-sm-8 input-group ">--}}
{{--                                <div class="input-group-prepend">--}}
{{--                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>--}}
{{--                                </div>--}}
{{--                                {!! html_date_input('create_at') !!}--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group row col-sm-6">--}}
{{--                            <label class="col-sm-2 col-form-label text-right">状态</label>--}}
{{--                            <div class="col-sm-8 input-group ">--}}
{{--                                <div class="input-group-prepend">--}}
{{--                                    <span class="input-group-text"><i class="fas fa-list-ol"></i></span>--}}
{{--                                </div>--}}
{{--                                <select name="status" class="form-control select2">--}}
{{--                                    <option value=""></option>--}}
{{--                                    @foreach($articles->statusItem() as $ind => $item)--}}
{{--                                        <option value="{{$ind}}">{{$item}}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group row col-sm-6 ">--}}
{{--                            <label class="col-sm-2 col-form-label text-right"></label>--}}
{{--                            <div class="col-sm-8 input-group ">--}}
{{--                                <div class="form-group margin-right-15">--}}
{{--                                    <button type="submit" class="btn btn-primary">--}}
{{--                                        搜索--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                                <div class="form-group">--}}
{{--                                    <button type="reset" class="btn btn-default">--}}
{{--                                        重置--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--            <div class="card-footer">--}}
{{--                <button type="button" onclick="delete_patch_fun('批量删除','{{route('articles.destroy', 0)}}','确认是否删除？')"--}}
{{--                        class="btn btn-sm btn-danger">批量删除--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div id="vue-app" class="card-body">--}}
{{--                <table class="table table-bordered table-hover dataTable list-data-table active">--}}
{{--                    <thead>--}}
{{--                        <tr>--}}
{{--                            <th style="width: 10px"><input class="check-all" type="checkbox"></th>--}}
{{--                            <th data-field="title" class="sorting">公告标题</th>--}}
{{--                            <th data-field="view_number" class="sorting">浏览次数</th>--}}
{{--                            <th data-field="push_source" class="sorting">发布来源</th>--}}
{{--                            <th data-field="created_at" class="sorting">创建时间</th>--}}
{{--                            <th data-field="status" class="sorting">状态</th>--}}
{{--                            <th class="" style="width: 180px">操作</th>--}}
{{--                        </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}

{{--                    </tbody>--}}
{{--                </table>--}}
{{--                <article-list></article-list>--}}
{{--            </div>--}}
{{--            <!-- /.box-body -->--}}
{{--            @include('common.page')--}}
{{--        </div>--}}
        <!-- /.box -->


    </section>
    <!-- /.content -->
@endsection

@section('footer')
    <script type="text/javascript">
        $(function () {
            // getDataList();
        })
    </script>
@endsection
