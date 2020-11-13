<table>
    <thead>
    <tr>
        <th>会员编号</th>
        <th>姓名</th>
        <th>微信昵称</th>
        <th>手机号码</th>
        <th>待提现佣金</th>
        <th>直接下线人数</th>
        <th>间接下线人数</th>
        <th>注册日期</th>
        <th>状态</th>
    </tr>
    </thead>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{ $item->member_no }}</td>
            <td>{{ $item->real_name ?? '' }}</td>
            <td>{{ $item->wx_name ?? '' }}</td>
            <td>{{ $item->mobile }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $item->reg_date }}</td>
            <td>{{ $item->statusItem($item->status) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
