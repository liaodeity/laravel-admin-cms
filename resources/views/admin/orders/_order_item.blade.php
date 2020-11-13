<tr>
    <th style="width:20%">订单编号:</th>
    <td>{{$order->order_no}}</td>
</tr>
<tr>
    <th style="width:20%">代理商名称:</th>
    <td>
        @if(check_admin_permission('show agents'))
            <a class="btn btn-link" onclick="view_fun('查看代理商信息','{{route('agents.show',$order->agent_id)}}')">{{$order->agent->agent_name}}</a>
            @else
            {{$order->agent->agent_name}}
        @endif
    </td>
</tr>
<tr>
    <th>订单总金额:</th>
    <td>{{$order->order_amount}}</td>
</tr>
<tr>
    <th>下单时间:</th>
    <td>{{$order->created_at}}</td>
</tr>
<tr>
    <th style="width:20%">收货人:</th>
    <td>{{$order->consignee ?? ''}}</td>
</tr>
<tr>
    <th>收货人号码:</th>
    <td>{{$order->consignee_phone ?? ''}}</td>
</tr>
<tr>
    <th>收货人详细地址:</th>
    <td>{{str_replace ('-','',($order->region->area_region_name ?? ''))}}{{$order->consignee_address ?? ''}}</td>
</tr>

<tr>
    <th>发货时间:</th>
    <td>{{$order->delivery_at}}</td>
</tr>
<tr>
    <th>发货快递公司:</th>
    <td>{{$order->delivery->name ?? ''}}</td>
</tr>
<tr>
    <th>发货快递编号:</th>
    <td>{{$order->delivery->delivery_no ?? ''}}</td>
</tr>
<tr>
    <th>代理商留言:</th>
    <td>
        {{$order->agent_remark ?? ''}}
    </td>
</tr>
<tr>
    <th>订单说明:</th>
    <td>
        {{$order->remark ?? ''}}
    </td>
</tr>
<tr>
    <th>是否从账户中扣除:</th>
    <td>{{$order->isAccountPayItem($order->is_account_pay)}}</td>
</tr>
<tr>
    <th>订单状态:</th>
    <td>{{$order->statusItem($order->status)}}</td>
</tr>
<tr>
    <th>修改时间:</th>
    <td>{{$order->updated_at}}</td>
</tr>
<tr>
    <th>创建时间:</th>
    <td>{{$order->created_at}}</td>
</tr>
