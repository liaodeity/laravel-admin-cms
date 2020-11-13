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
                                    class="text-red">*</span>记录说明</label>

                            <div class="col-sm-10">
                                <textarea name="Log[content]" id="" class="form-control" cols="30" rows="5" maxlength="200"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>订单状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($order->statusItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="Order[status]" @if(isset($order->status) && $order->status == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="express-delivery form-group hide">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>快递公司</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="Delivery[delivery_id]" id="">
                                    <option value=""></option>
                                    @foreach(\App\Entities\ExpressDelivery::getList() as $item)
                                    <option value="{{$item->id}}"  @if(isset($order->delivery->delivery_no) && $order->delivery->delivery_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="express-delivery form-group hide">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>快递单号</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="" name="Delivery[delivery_no]" maxlength="100" value="{{$order->delivery->delivery_no ?? ''}}" placeholder="">
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
    <script>

        $ (function () {
            $ ("input[name='Order[status]']").change (function () {
                var status = $(this).val();
                if(status == '{{\App\Entities\Order::YES_DELIVERY_STATUS}}'){
                    $(".express-delivery").removeClass('hide')
                }else{
                    $(".express-delivery").addClass('hide')
                }
            })
            $ ("input[name='Order[status]']:checked").change()
        })
    </script>
@endsection
