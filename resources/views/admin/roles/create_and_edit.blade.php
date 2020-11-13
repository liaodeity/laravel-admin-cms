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
                    <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? '' }}" onsubmit="return false;">
                        @method($method ?? '')
                        <input id="rules" type="hidden" name="RoleInfo[role_value]" value="{{$role->role_value ?? ''}}" >
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>角色名称</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="RoleInfo[name]" id="" value="{{$role->name ?? ''}}" autocomplete="off" maxlength="50" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>角色说明</label>

                            <div class="col-sm-10">
                                <textarea class="form-control" name="RoleInfo[desc]" autocomplete="off" cols="30" maxlength="1000" rows="5">{{$role->desc ?? ''}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>角色权限列表</label>

                            <div class="col-sm-10">
                                <ul id="roleTree" class="ztree" style="height: 250px;overflow: auto;"></ul>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($role->statusItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="RoleInfo[status]" @if(isset($role->status) && $role->status == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info width-100 margin-top-15" data-confirm="确认保存？">保
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
