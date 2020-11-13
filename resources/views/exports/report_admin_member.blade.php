<table>
    <thead>
    <tr>
        <th>统计日期</th>
        <th>新增数量</th>
    </tr>
    </thead>
    <tbody>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{$item->count_type}}</td>
            <td>{{$item->count}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
