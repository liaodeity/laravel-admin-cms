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
                    <form id="form-iframe-add" class="form-horizontal" action="{{route ('menu.store')}}"
                          onsubmit="return false;">
                        <input type="hidden" name="Menu[auth_name]" value="">
                        <input type="hidden" name="Menu[module]" value="admin">
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>菜单名称</label>

                            <div class="col-sm-10">
                                <input type="text" name="Menu[title]" class="form-control" value="" id="inputName"
                                       placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>路由地址</label>

                            <div class="col-sm-10">
                                <input type="text" name="Menu[route_url]" value="" class="form-control" id="inputName"
                                       placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>上级菜单名称</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="Menu[pid]" id="">
                                    <option value="0"></option>
                                    @foreach($menus as $menu)
                                        <option value="{{$menu->id}}">{{$menu->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>菜单排序</label>

                            <div class="col-sm-10">
                                <input type="number" name="Menu[sort]" class="form-control" id="inputName" value="0"
                                       placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        <label><input name="Menu[status]" value="1" type="radio" checked>显示</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label><input name="Menu[status]" value="2" type="radio">隐藏</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info width-100 margin-top-15" data-confirm="确认保存？">
                                    保
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
