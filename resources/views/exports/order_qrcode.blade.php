<table>
    <thead>
    <tr>
        <th>二维码编号</th>
        <th>二维码内容</th>
        <th>代理商名称</th>
        <th>产品名称</th>
        <th>执行标准</th>
        <th>生成批号</th>
        <th>生产日期</th>
        <th>规格</th>
        <th>质检员</th>
        <th>保质期</th>
        <th>授权区域</th>
    </tr>
    </thead>
    <tbody>
    @foreach($exportData as $item)
        <tr>
            <td>{{ $item->qrcode_no }}</td>
            <td>{{ $item->getQrCodeUrl($item->qrcode_no) }}</td>
            <td>{{ $item->agent->agent_name ?? '' }}</td>
            <td>{{ $item->orderProduct->title ?? '' }}</td>
            <td>{{ $item->standard_no }}</td>
            <td>{{ $item->generate_batch }}</td>
            <td>{{ $item->production_date }}</td>
            <td>{{ $item->specification }}</td>
            <td>{{ $item->quality_inspector }}</td>
            <td>{{ $item->orderProduct->shelf_life ?? '' }}</td>
            <td>{{ $item->region_name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
