{{--<table>--}}
{{--    <thead>--}}
{{--    <tr>--}}
{{--        <th>订单编号</th>--}}
{{--        <th>代理商名称</th>--}}
{{--        <th>总金额</th>--}}
{{--        <th>下单时间</th>--}}
{{--        <th>发货时间</th>--}}
{{--        <th>订单状态</th>--}}
{{--    </tr>--}}
{{--    </thead>--}}
{{--    <tbody>--}}
{{--    @foreach($exportData as $item)--}}
{{--        <tr>--}}
{{--            <td>{{ $item->order_no }}</td>--}}
{{--            <td>{{ $item->agent->agent_name ?? '' }}</td>--}}
{{--            <td>{{ $item->order_amount ?? '' }}</td>--}}
{{--            <td>{{ $item->created_at }}</td>--}}
{{--            <td>{{$item->delivery_at}}</td>--}}
{{--            <td>{{ $item->statusItem($item->status) }}</td>--}}
{{--        </tr>--}}
{{--    @endforeach--}}
{{--    </tbody>--}}
{{--</table>--}}
<table>
    <thead>
    <tr>
        <th>订单编号</th>
        <th>代理商名称</th>
        <th>产品名称</th>
        <th>规格</th>
        <th>型号</th>
        <th>仓库</th>
        <th>单位</th>
        <th>数量</th>
        <th>单价</th>
        <th>金额</th>
        <th>预交日期</th>
        <th>摘要</th>
    </tr>
    </thead>
    <tbody>
    @foreach($exportData as $export)
        @foreach($export->itemOrderProducts($export->id) as $item)
            @foreach($item->products as $key =>  $product)
                <tr>
                    <td>{{ $export->order_no ?? '' }}</td>
                    <td>{{ $export->agent->agent_name ?? '' }}</td>
                    <td>{{ $product->title ?? '' }}</td>
                    <td>{{ $product->specification ?? '' }}</td>
                    <td>{{ $product->model ?? '' }}</td>
                    <td>{{ $product->warehouse ?? '' }}</td>
                    <td>{{ $product->unit ?? '' }}</td>
                    <td>{{ $product->number ?? '' }}</td>
                    <td>{{ $product->price ?? '' }}</td>
                    <td>{{ ($product->number ?? 0) * ($product->price ?? 0) }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        @endforeach
    @endforeach
    </tbody>
</table>
