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
                    <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? ''}}" onsubmit="return false;">
                        @method($method ?? '')
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>快递名称</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="ExpressDelivery[name]" maxlength="50" autocomplete="off" value="{{$expressDelivery->name ?? ''}}" placeholder="">
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group ">
                                 <span class="input-group-btn"><button type="button" class="btn btn-default btn-flat">常见快递选择输入：</button></span><select id="express" class="form-control">
                                    <option value=""></option>
                                    @foreach($expressDelivery->usedCodeItem() as $code => $item)
                                        <option data-code="{{$code}}" value="{{$item}}" @if(isset($expressDelivery->com_code) && $expressDelivery->com_code == $item) selected @endif>{{$item}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>快递接口标识</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="ExpressDelivery[com_code]" maxlength="50" autocomplete="off" value="{{$expressDelivery->com_code ?? ''}}" placeholder="">

                            </div>
                            <div class="col-sm-4">
                                <a href="{{route('expressDeliveries.all_code')}}" target="_blank" style="    line-height: 33px;">所有对照标识</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>排序</label>

                            <div class="col-sm-2">
                                <input type="number" class="form-control" name="ExpressDelivery[sort]" onkeyup="value=keyupNumber(this.value, 0, 999)" autocomplete="off" id="" value="{{$expressDelivery->sort ?? 0}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info width-100 margin-top-15" data-confirm="">保
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
<script type="text/javascript">
    $(function () {
        $("#express").change(function () {
            var name = $(this).val();
            var code = $("#express option:selected").data('code');
            $("input[name='ExpressDelivery[name]']").val(name)
            $("input[name='ExpressDelivery[com_code]']").val(code)
        })
    })
</script>
@endsection
