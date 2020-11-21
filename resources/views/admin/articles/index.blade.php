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
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="float-right">
                    <a href="{{route('articles.create')}}"
                       class="btn btn-sm btn-info pull-right"><i
                            class="fa fa-plus"></i> 添加
                    </a>
                </div>
                <div class="float-left">
                    <button id="search-choice" type="button" class="btn btn-info btn-sm ">
                        <i class="fa fa-filter"></i><span class="hidden-xs">&nbsp;&nbsp;筛选</span>
                    </button>
                </div>
            </div>
            <div id="filter-body" class="card-body hidden">
                <form class="form-horizontal list-search-form active" action="" onsubmit="return false;">
                    <div class="card-body row">
                        <div class="form-group row col-sm-6">
                            <label class="col-sm-2 col-form-label text-right">公告标题</label>
                            <div class="col-sm-8 input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                </div>
                                <input type="text" name="title" class="form-control" id=""
                                       autocomplete="off" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row col-sm-6">
                            <label class="col-sm-2 col-form-label text-right">发布来源</label>
                            <div class="col-sm-8 input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                </div>
                                <input type="text" name="push_source" class="form-control" id=""
                                       autocomplete="off" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row col-sm-6">
                            <label class="col-sm-2 col-form-label text-right">发布时间</label>
                            <div class="col-sm-8 input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                                {!! html_date_input('create_at') !!}
                            </div>
                        </div>
                        <div class="form-group row col-sm-6">
                            <label class="col-sm-2 col-form-label text-right">状态</label>
                            <div class="col-sm-8 input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                </div>
                                <select name="status" class="form-control select2">
                                    <option value=""></option>
                                    @foreach($articles->statusItem() as $ind => $item)
                                        <option value="{{$ind}}">{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row col-sm-6 ">
                            <label class="col-sm-2 col-form-label text-right"></label>
                            <div class="col-sm-8 input-group ">
                                <div class="form-group margin-right-15">
                                    <button type="submit" class="btn btn-primary">
                                        搜索
                                    </button>
                                </div>
                                <div class="form-group">
                                    <button type="reset" class="btn btn-default">
                                        重置
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <button type="button" onclick="delete_patch_fun('批量删除','{{route('articles.destroy', 0)}}','确认是否删除？')"
                        class="btn btn-sm btn-danger">批量删除
                </button>
            </div>
            <div class="card-body">
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
                <el-table
                    :data="tableData"
                    @selection-change="handleSelectionChange"
                    v-loading="loading"
                    max-height="500"
                    stripe
                    style="width: 100%">
                    </el-table-column>
                    <el-table-column type="expand">
                        <template slot-scope="props">
                            <el-form label-position="left" inline class="demo-table-expand">
                                <el-form-item label="健康报告">
                                    <el-tag type="success" v-if="props.row.healthy_report_file">已上传</el-tag>
                                </el-form-item>
                                <el-form-item label="已安装设备">
                                    <div class="tag-group">
                                        <el-tag type="info" v-for="item in props.row.pat_device_sub" effect="plain" style="margin-right: 5px;">{{item}}</el-tag>
                                    </div>
                                </el-form-item>
                                <el-form-item label="服务协议">
                                    <el-tag type="success" v-if="props.row.agreement_file">已上传</el-tag>
                                </el-form-item>
                                <el-form-item label="失败证明">
                                    <el-tag type="success" v-if="props.row.fail_file">已上传</el-tag>
                                </el-form-item>
                                <el-form-item label="服务时间">
                                    <span>{{props.row.agreement_date}}</span>
                                </el-form-item>
                                <el-form-item label="户籍地居委">
                                    <span>{{props.row.household_community}}</span>
                                </el-form-item>
                                <el-form-item label="居住地居委">
                                    <span>{{props.row.resident_community}}</span>
                                </el-form-item>
                            </el-form>
                        </template>
                    </el-table-column>
                    <el-table-column
                        prop="addtime"
                        width="150px"
                        label="登记时间">
                    </el-table-column>
                    <el-table-column
                        prop="serial_no"
                        label="待办单编号"
                        width="100">
                    </el-table-column>
                    <el-table-column
                        prop="flow_name"
                        label="业务类型">
                    </el-table-column>
                    <el-table-column
                        prop="accountname"
                        label="名称"
                    >
                    </el-table-column>
                    <el-table-column
                        prop="vc_id_number"
                        label="身份证">
                    </el-table-column>

                    <el-table-column
                        prop="service_org_mobile"
                        label="平安通电话">
                    </el-table-column>
                    <el-table-column
                        prop="execute_msg"
                        label="施工结果/失败原因">
                    </el-table-column>
                    <el-table-column
                        prop="is_fail"
                        label="竣工失败">
                        <template slot-scope="scope">
                            <el-tag type="success" v-if="scope.row.is_fail == 1">是</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column
                        prop="if_send"
                        label="推送状态"
                        width="100">
                        <template slot-scope="scope">
                            <el-tooltip class="item" effect="dark" v-if="scope.row.error_msg != ''" :content="scope.row.error_msg" placement="left">
                                <i class="el-icon-warning"></i>
                            </el-tooltip>
                            <el-tag type="success" v-if="scope.row.if_send == 1">已推送</el-tag>
                            <el-tag v-if="scope.row.if_send == 2">推送中</el-tag>
                            <el-tag type="danger" v-if="scope.row.if_send == 3">推送失败</el-tag>
                            <el-tag type="info" v-if="scope.row.if_send == 0">待处理</el-tag>
                            <el-tag type="info" v-if="scope.row.if_send == -1">无效数据</el-tag>

                        </template>
                    </el-table-column>
                </el-table>
                <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="formData.currentPage"
                    :page-sizes="pageSizeList"
                    :page-size="formData.pageSize"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="dataTotal">
                </el-pagination>
            </div>
            <!-- /.box-body -->
            @include('common.page')
        </div>
        <!-- /.box -->


    </section>
    <!-- /.content -->
@endsection

@section('footer')
    <script type="text/javascript">
        $(function () {
            getDataList();
        })
    </script>
@endsection
