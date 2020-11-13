<table>
    <thead>
    <tr>
        <th>会员名称</th>
        <th>下线会员数量</th>
    </tr>
    </thead>
    <tbody>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{\App\Entities\Member::showName($item->member_id)}}</td>
            <td>{{$item->count}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
