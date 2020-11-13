@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="box-body">
                <div class="box box-primary">
                    <div class="table">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:20%">角色名称:</th>
                                <td>{{$role->name}}</td>
                            </tr>

                            <tr>
                                <th style="width:20%">角色说明:</th>
                                <td>{{$role->desc}}</td>
                            </tr>
                            <tr>
                                <th>角色权限列表：</th>
                                <td>
                                    @if($role->role->name == 'super')
                                        所有权限
                                        @else
                                    <ul id="roleTree" class="ztree" style="height: 250px;overflow: auto;"></ul>
                                        @endif
                                </td>
                            </tr>
                            <tr>
                            <tr>
                                <th>状态:</th>
                                <td>{{$role->statusItem($role->status)}}</td>
                            </tr>
                            <tr>
                                <th>修改时间:</th>
                                <td>{{$role->updated_at}}</td>
                            </tr>
                            <tr>
                                <th>创建时间:</th>
                                <td>{{$role->created_at}}</td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <!-- /.row -->
        </section>


    </div>
@endsection

@section('footer')
    <SCRIPT type="text/javascript">
        <!--
        var setting = {
            check: {
                enable: true
            },
            data: {
                simpleData: {
                    enable: true
                }
            },
            // chkboxType: {"Y": "ps", "N": "ps"},
            callback: {
                onCheck: onCheck,
                onClick: onClick
            }
        };

        var zNodes = {!! $auth_str !!};

        function onClick(e,treeId, treeNode) {
            var zTree = $.fn.zTree.getZTreeObj(treeId);
            zTree.expandNode(treeNode);
        }

        function onCheck (e, treeId, treeNode) {
            var zTree = $.fn.zTree.getZTreeObj (treeId);
            zTree.expandNode(treeNode);
            var nodes = zTree.getCheckedNodes (true);
            var moduleids = "";
            for (i = 0; i < nodes.length; i++) {
                moduleids = moduleids + nodes[ i ].id + ",";
            }
            moduleids = moduleids.substring (0, moduleids.length - 1);

            $ ("#rules").val (moduleids);
        }

        $ (document).ready (function () {
            zTree = $.fn.zTree.init ($ ("#roleTree"), setting, zNodes);

            // index = layer.alert ('请不要随意的调整【权限列表】的内容，否则导致权限异常后果自负！！！', {icon: 2, btn: '已知晓'});
            // layer.title ('严重警告', index);
        });
        //-->
    </SCRIPT>
@endsection
