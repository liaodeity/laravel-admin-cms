@inject('payTrade','App\Entities\PayTrade')
<table>
    <thead>
    <tr>
        <th >交易时间</th>
        <th >订单编号</th>
        <th >第三方交易单号</th>
        <th >支付方式</th>
        <th >交易金额</th>
    </tr>
    </thead>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{$item->trade_at}}</td>
            <td>{{$item->order_no}}</td>
            <td>{{$item->transaction_no}}</td>
            <td>{{$payTrade->typeItem($item->type)}}</td>
            <td>{{$item->trade_price}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
