<table>
    <thead>
    <tr>
        <th>代理商名称</th>
        <th>代理商电话</th>
        <th>会员数量</th>
        <th>佣金数</th>
    </tr>
    </thead>
    <tbody>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{\App\Entities\Agent::showName ($item->agent_id)}}</td>
            <td>{{\App\Entities\Agent::showMobile ($item->agent_id)}}</td>
            <td>{{$item->number??0}}</td>
            <td>{{$item->amount ?? 0}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
