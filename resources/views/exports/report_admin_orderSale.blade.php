@inject('RegionPresenter', 'App\Presenters\RegionPresenter')
@inject('ProductPresenter', 'App\Presenters\ProductPresenter')
<table>
    <thead>
    <tr>
        <th >所属区域</th>
        <th >商品名称</th>
        <th >销售数量</th>
        <th >小计</th>
    </tr>
    </thead>
    <tbody>
    <tbody>
    @foreach($exportData as $item)
    <tr>
        <td>{{$RegionPresenter->showName($item->region_id)}}</td>
        <td>{{$ProductPresenter->showName($item->product_id)}}</td>
        <td>{{$item->order_number ?? 0}}</td>
        <td>{{$item->order_number ?? 0}}</td>
    </tr>
        @endforeach
    </tbody>
</table>
