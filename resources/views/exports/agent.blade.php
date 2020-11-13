<table>
    <thead>
    <tr>
        <th>代理商编号</th>
        <th>用户名</th>
        <th>代理商名称</th>
        <th>微信昵称</th>
        <th>联系人</th>
        <th>联系电话</th>
        <th>办公地址</th>
        <th>直接下线人数</th>
        <th>间接下线人数</th>
        <th>加盟日期</th>
        <th>状态</th>
    </tr>
    </thead>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{ $item->agent_no }}</td>
            <td>{{ $item->username ?? '' }}</td>
            <td>{{ $item->agent_name ?? '' }}</td>
            <td>{{ $item->wx_name }}</td>
            <td>{{$item->contact_name}}</td>
            <td>{{$item->contact_phone}}</td>
            <td>{{$item->office_address}}</td>
            <td>{{$item->sort}}</td>
            <td>{{$item->sort}}</td>
            <td>{{$item->join_date}}</td>
            <td>{{ $item->statusItem($item->status) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
