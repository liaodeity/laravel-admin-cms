<table>
    <thead>
    <tr>
        <th >代理商名称</th>
        <th>代理商电话</th>
        <th>商品名称</th>
        <th >总金额</th>
        <th >总频次</th>
        <th>进货数量</th>
    </tr>
    </thead>
    <tbody>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{\App\Entities\Agent::showName ($item->agent_id)}}</td>
            <td>{{\App\Entities\Agent::showMobile ($item->agent_id)}}</td>
            <td>{{\App\Entities\Product::showName ($item->product_id)}}</td>
            <td>{{$item->order_amount ?? 0}}</td>
            <td>{{$item->order_number ?? 0}}</td>
            <td>{{$item->product_number ?? 0}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
