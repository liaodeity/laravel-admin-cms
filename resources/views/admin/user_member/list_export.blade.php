<table>
    <thead>
    <tr>
        <th>登录账号</th>
        <th>电子邮箱</th>
        <th>联系电话</th>
        <th>真实姓名</th>
        <th>性别</th>
        <th>地址</th>
        <th>登录次数</th>
        <th>最后登录时间</th>
        <th>状态</th>
        <th>创建时间</th>
    </tr>
    </thead>
    <tbody>
    @foreach($details as $item)
        <tr>
            <td>{{$item->name ?? ''}}</td>
            <td>{{$item->email ?? ''}}</td>
            <td>{{$item->telephone ?? ''}}</td>
            <td>{{$item->real_name ?? ''}}</td>
            <td>{{$item->gender ?? ''}}</td>
            <td>{{$item->address ?? ''}}</td>
            <td>{{$item->login_count ?? ''}}</td>
            <td>{{$item->last_login_at ?? ''}}</td>
            <td>{{$item->status ?? ''}}</td>
            <td>{{$item->created_at ?? ''}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
