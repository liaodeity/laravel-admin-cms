@extends('layouts.app')

@section('style')

@endsection

@section('content')
    <div class="layui-fluid">
        <div class=" bg-white  system-gui-show">
            <div class="layui-row">
                <div class="layui-tab layui-tab-brief">
                    <ul class="layui-tab-title">
                        <li class="layui-this">基本信息</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <table class="layui-table table-show">
                                <colgroup>
                                    <col width="15%">
                                    <col width="">
                                    <col>
                                </colgroup>
                                <tbody>
                                <tr>
                                    <th>上级菜单</th>
                                    <td>{{$menu->pidMenu->title ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>菜单标识名称</th>
                                    <td>{{$menu->menu_name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>权限名称</th>
                                    <td>{{$menu->auth_name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>类型</th>
                                    <td>{{\App\Enums\MenuTypeEnum::toLabel ($menu->type ?? '')}}</td>
                                </tr>
                                <tr>
                                    <th>排序</th>
                                    <td>{{$menu->sort ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>路由地址</th>
                                    <td>{{$menu->href ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>菜单名称</th>
                                    <td>{{$menu->title ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>图标</th>
                                    <td>{{$menu->icon ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>快捷方式</th>
                                    <td>{{\App\Enums\SwitchYesEnum::toLabel ($menu->is_shortcut ?? '')}}</td>
                                </tr>
                                <tr>
                                    <th>状态</th>
                                    <td>{{\App\Enums\MenuStatusEnum::toLabel ($menu->status ?? '')}}</td>
                                </tr>
                                <tr>
                                    <th>创建时间</th>
                                    <td>{{$menu->created_at ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>更新时间</th>
                                    <td>{{$menu->updated_at ?? ''}}</td>
                                </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')

@endsection
