@extends('common.ele')

@section('style')

@endsection

@section('content')
    <template>
        <el-tabs value="index" v-model="tabDefaultValue" type="border-card" @tab-click="handleTabClick">
            <el-tab-pane name="index" label="登记竣工记录">
                <div class="row mar-b-15">
                    <el-col :span="12">
                        <el-button type="primary" icon="el-icon-search" @click="filterVisible = !filterVisible">筛选</el-button>
                    </el-col>
                    <el-col :span="12">
                        <el-button class="float-right" type="primary" @click="dialogSearchVisible = true">登记竣工</el-button>
                    </el-col>
                </div>

                <el-collapse-transition>
                    <el-form label-position="right" :inline="true" v-show="filterVisible" :model="formData" class="search-form-inline">
                        <el-form-item label="登记时间">
                            <el-date-picker
                                v-model="formData.addtime"
                                type="datetimerange"
                                range-separator="至"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期" class="width-100p">
                            </el-date-picker>
                        </el-form-item>
                        <el-form-item label="待办单编号">
                            <el-input v-model="formData.serial_no" placeholder=""></el-input>
                        </el-form-item>
                        <el-form-item label="名称/身份证">
                            <el-input v-model="formData.keyword" placeholder=""></el-input>
                        </el-form-item>
                        <el-form-item label="推送状态">
                            <el-select v-model="formData.if_send" placeholder="请选择" clearable="" class="width-100p">
                                <el-option
                                    v-for="item in selectVal.if_send"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
                        </el-form-item>
                    </el-form>
                </el-collapse-transition>
                <el-table
                    :data="tableData"
                    style="width: 100%">
                    <el-table-column
                        prop="date"
                        label="日期"
                        width="180">
                    </el-table-column>
                    <el-table-column
                        prop="name"
                        label="姓名"
                        width="180">
                    </el-table-column>
                    <el-table-column
                        prop="address"
                        label="地址">
                    </el-table-column>
                </el-table>
                <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="pager.current"
                    :page-sizes="pager.sizeList"
                    :page-size="pager.size"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="pager.total">
                </el-pagination>
            </el-tab-pane>
        </el-tabs>

    </template>



    <!-- Content Header (Page header) -->
    {{--    <div class="content-header">--}}
    {{--        <div class="container-fluid">--}}
    {{--            <div class="row mb-2">--}}
    {{--                <div class="col-sm-6">--}}
    {{--                    <h1 class="m-0">公告列表</h1>--}}
    {{--                </div><!-- /.col -->--}}
    {{--                <div class="col-sm-6">--}}
    {{--                    <ol class="breadcrumb float-sm-right">--}}
    {{--                        <li class="breadcrumb-item"><a href="/admin">首页</a></li>--}}
    {{--                        <li class="breadcrumb-item ">通知公告</li>--}}
    {{--                        <li class="breadcrumb-item active">公告列表</li>--}}
    {{--                    </ol>--}}
    {{--                </div><!-- /.col -->--}}
    {{--            </div><!-- /.row -->--}}
    {{--        </div>--}}
    {{--    </div>--}}

    {{--    <!-- Main content -->--}}
    {{--    <section id="vue-app" class="content">--}}
    {{--        <article-list></article-list>--}}
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


    {{--    </section>--}}
    <!-- /.content -->
@endsection

@section('footer')
    <script src=""></script>
    <script>
        // let Main = {
        //     data() {
        //         return {
        //             logo:"https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
        //             chinaImg:"https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
        //             americaImg:"https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
        //             wechat:{
        //                 wechatImg:"https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
        //                 isWechat:false
        //             },
        //             qq:{
        //                 qqImg:"https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
        //                 isQq:false,
        //             },
        //             menu:{
        //                 userBgcolor:'#f0f2f5'
        //             },
        //             github:'https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png',
        //             langLogo:'https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png',
        //             avatar:'https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png',
        //             tabDefaultValue: 'index',
        //             filterVisible: false,
        //             formData: {},
        //             selectVal: {},
        //             pager: {
        //                 current: 1,
        //                 total: 0,
        //                 size: 10,
        //                 sizeList: [10, 20, 30, 40],
        //             },
        //             tableData: [{
        //                 date: '2016-05-02',
        //                 name: '王小虎',
        //                 address: '上海市普陀区金沙江路 1518 弄'
        //             }, {
        //                 date: '2016-05-04',
        //                 name: '王小虎',
        //                 address: '上海市普陀区金沙江路 1517 弄'
        //             }, {
        //                 date: '2016-05-01',
        //                 name: '王小虎',
        //                 address: '上海市普陀区金沙江路 1519 弄'
        //             }, {
        //                 date: '2016-05-03',
        //                 name: '王小虎',
        //                 address: '上海市普陀区金沙江路 1516 弄'
        //             }]
        //         }
        //     },
        //     created: function () {
        //         console.log(222);
        //     },
        //     methods: {
        //         onSearch() {
        //
        //         },
        //         handleTabClick() {
        //
        //         },
        //         handleSizeChange(val) {
        //             this.formData.pageSize = val;
        //             this.getList();
        //         },
        //         handleCurrentChange(val) {
        //             this.formData.currentPage = val;
        //             this.getList();
        //         }
        //     }
        // }
        // var Ctor = Vue.extend(Main)
        // Vue.prototype.$ELEMENT = {size: 'small'};
        // new Ctor().$mount('#app')
    </script>
@endsection
