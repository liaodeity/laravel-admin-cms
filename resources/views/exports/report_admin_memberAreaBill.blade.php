@inject('RegionPresenter', 'App\Presenters\RegionPresenter')
<table>
    <thead>
    <tr>
        <th rowspan="2">统计日期</th>
        <th rowspan="2">会员地区</th>
        <th colspan="3">佣金金额</th>
    </tr>
    <tr>
        <th>已发放金额</th>
        <th>未发放金额</th>
        <th>小计</th>
    </tr>
    </thead>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{$item->count_type}}</td>
            <td>{{$RegionPresenter->showName($item->region_id)}}</td>
            <td>{{$item->yes_pay ?? 0}}</td>
            <td>{{$item->no_pay ?? 0}}</td>
            <td>{{$item->amount ?? 0}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
