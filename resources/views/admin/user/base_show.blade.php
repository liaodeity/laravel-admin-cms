<tr>
    <th>用户账号</th>
    <td>{{$user->name ?? ''}}</td>
</tr>
<tr>
    <th>电子邮箱</th>
    <td>{{$user->email ?? ''}}</td>
</tr>
<tr>
    <th>真实姓名</th>
    <td>{{$user->info->real_name ?? ''}}</td>
</tr>
<tr>
    <th>真实姓名</th>
    <td>{{\App\Libs\Parameter::genderItem ($user->info->gender ?? '') }}</td>
</tr>
<tr>
    <th>联系电话</th>
    <td>{{$user->info->telephone ?? ''}}</td>
</tr>
<tr>
    <th>地址</th>
    <td>{{$user->info->address ?? ''}}</td>
</tr>
