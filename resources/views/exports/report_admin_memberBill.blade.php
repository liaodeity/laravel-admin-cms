<table>
    <thead>
    <tr>
        <th rowspan="2" >统计日期</th>
        <th rowspan="2" >会员姓名</th>
        <th colspan="3" >佣金金额</th>
    </tr>
    <tr>
        <th >已发放金额</th>
        <th >未发放金额</th>
        <th >小计</th>
    </tr>
    </thead>
    <tbody>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{$item->count_type}}</td>
            <td>{{\App\Entities\Member::showName($item->member_id)}}</td>
            <td>{{$item->yes_pay}}</td>
            <td>{{$item->no_pay}}</td>
            <td>{{$item->amount}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
