@inject('ProductPresenter', 'App\Presenters\ProductPresenter')
<table>
    <thead>
    <tr>
        <th rowspan="2" data-field="task" class="">统计日期</th>
        <th rowspan="2" data-field="task" class="">商品名称</th>
        <th colspan="3" data-field="progress" class="">扫码次数</th>
{{--        <th colspan="3" data-field="progress" class="">小计</th>--}}
    </tr>
    <tr>
        <th>总数</th>
        <th>会员员</th>
        <th class="border-right-width-1px">非会员</th>
{{--        <th>总数</th>--}}
{{--        <th>会员员</th>--}}
{{--        <th class="border-right-width-1px">非会员</th>--}}
    </tr>
    </thead>
    <tbody>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{$item->count_type}}</td>
            <td>{{$ProductPresenter->showName($item->product_id)}}</td>
            <td>{{$item->member_num}}</td>
            <td>{{$item->no_member_num ?? 0}}</td>
            <td>{{$item->member_num+$item->no_member_num}}</td>
{{--            <td>{{$item->member_num}}</td>--}}
{{--            <td>{{$item->no_member_num ?? 0}}</td>--}}
{{--            <td>{{$item->member_num+$item->no_member_num}}</td>--}}
        </tr>
    @endforeach
    </tbody>
</table>
