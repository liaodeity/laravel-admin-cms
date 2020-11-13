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
                        <table class="table text-center table-bordered no-margin">
                            <thead>
                            <tr>
                                <th>商品名称</th>
                                <th>标准</th>
                                <th>规格</th>
                                <th>单价</th>
                                <th width="120">当前订单数量</th>
                                <th width="100">售后数量</th>
                                <th>卡片底色</th>
                                <th>是否放卡</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orderSale->order->itemOrderProducts($orderSale->order_id) as $item)
                                @foreach($item->products as $key =>  $product)
                                    <tr>
                                        @if($key === 0)
                                            <td rowspan="{{$item->count}}"
                                                style="vertical-align:middle">{{$product->title}}</td>
                                            <td rowspan="{{$item->count}}"
                                                style="vertical-align:middle">{{$product->standard_no}}</td>

                                        @endif
                                            <td
                                                style="vertical-align:middle">{{$product->specification}}</td>
{{--                                        <td class="border-right-width-1px">{{$product->generate_batch}}</td>--}}
                                        <td class="border-right-width-1px">{{$product->price}}</td>
                                        <td class="border-right-width-1px">{{$product->number}}</td>
                                        <td>
                                            <input type="hidden"  name="OrderSaleProduct[{{$product->id ?? 0}}][id]" value="{{$product->id}}">
                                            <input type="hidden"  name="OrderSaleProduct[{{$product->id ?? 0}}][product_id]" value="{{$product->product_id}}">
                                            <input type="number" class="form-control" onchange="value=keyupNumber(this.value,0,{{$product->number}})" onkeyup="value=keyupNumber(this.value,0,{{$product->number}})" name="OrderSaleProduct[{{$product->id ?? 0}}][number]" value="{{$product->orderSaleProduct($orderSale->id)->number ?? 0}}"></td>
                                        @if($key === 0)
                                            <td rowspan="{{$item->count}}">
                                                <small class="label  width-100"
                                                       style="background: {{$product->product->card_background ?? ''}} !important;">
                                                    &nbsp;
                                                </small>
                                            </td>
                                        @endif
                                            <td>
                                                {{$product->isPutCardItem($product->is_put_card ?? '')}}
                                            </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>

                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>快递公司</label>

                            <div class="col-sm-10 ">
                                <div class="checkbox">{{$orderSale->saleDelivery->name ?? ''}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>快递单号</label>

                            <div class="col-sm-10 ">
                                <div class="checkbox">{{$orderSale->saleDelivery->delivery_no ?? ''}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red"></span>申请说明</label>

                            <div class="col-sm-10 ">
                                <div class="checkbox">{{$orderSale->apply_desc ?? ''}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>处理说明</label>

                            <div class="col-sm-10">
                                <textarea name="OrderSale[process_desc]" maxlength="500" id="" class="form-control" cols="30" rows="5">{{$orderSale->process_desc ?? ''}}</textarea>
                            </div>
                        </div>
                        <div class="express-delivery form-group hide">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>回寄快递公司</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="ExpressDeliveryInfo[delivery_id]" id="">
                                    <option value=""></option>
                                    @foreach(\App\Entities\ExpressDelivery::getList() as $item)
                                        <option value="{{$item->id}}"  @if(isset($orderSale->sendBackDelivery->delivery_no) && $orderSale->sendBackDelivery->delivery_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="express-delivery form-group hide">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>回寄快递单号</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="ExpressDeliveryInfo[delivery_no]" maxlength="50" id="" value="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>售后状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($orderSale->statusItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="OrderSale[status]" @if(isset($orderSale->status) && $orderSale->status == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
    <script>

        $ (function () {
            $ ("input[name='OrderSale[status]']").change (function () {
                var status = $(this).val();
                if(status == '{{\App\Entities\OrderSale::COMPLETE_STATUS}}'){
                    $(".express-delivery").removeClass('hide')
                }else{
                    $(".express-delivery").addClass('hide')
                }
            })
            $ ("input[name='OrderSale[status]']:checked").change()
        })
    </script>

@endsection
