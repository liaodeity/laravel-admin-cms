<tr>
    <th style="width:20%">姓名:</th>
    <td>{{$member->real_name ?? ''}}</td>
</tr>
<tr>
    <th>微信号:</th>
    <td>{{$member->wx_account ?? ''}}</td>
</tr>
<tr>
    <th style="width:20%">微信昵称:</th>
    <td>{{$member->wx_name ?? ''}}</td>
</tr>
<tr>
    <th style="width:20%">二维码名片:</th>
    <td>
        @if(isset($member->wx_qr_code) && $member->wx_qr_code)
            <img src="{{show_picture_to_id($member->wx_qr_code)}}" width="200">
        @endif
    </td>
</tr>
<tr>
    <th>会员编号:</th>
    <td>{{$member->member_no ?? ''}}</td>
</tr>
<tr>
    <th>手机号码:</th>
    <td>{{$member->mobile ?? ''}}</td>
</tr>
<tr>
    <th>出生日期</th>
    <td>{{$member->birthday ?? ''}}</td>
</tr>
<tr>
    <th>性别</th>
    <td>{{$member->gender ?? ''}}</td>
</tr>
<tr>
    <th>籍贯区域</th>
    <td>{{$member->nativeRegion->area_region_name ?? ''}}</td>
</tr>
<tr>
    <th>常驻区域</th>
    <td>{{$member->residentRegion->area_region_name ?? ''}}</td>
</tr>
<tr>
    <th>常驻住址</th>
    <td>{{$member->resident_address ?? ''}}</td>
</tr>
<tr>
    <th>工种</th>
    <td>{{$member->work_type ?? ''}}</td>
</tr>
<tr>
    <th>从业年限</th>
    <td>{{$member->working_year ?? ''}}</td>
</tr>
<tr>
    <th>业务渠道</th>
    <td>
        {{$member->business_channel ??''}}
    </td>
</tr>
<tr>
    <th>下级人数:</th>
    <td>{{$member->directChildNumber()}}</td>
</tr>
<tr>
    <th>累计总佣金:</th>
    <td>{{$member->allBillAmount()}}</td>
</tr>
<tr>
    <th>待提现佣金:</th>
    <td>{{$member->noPayBillAmount()}}</td>
</tr>
<tr>
    <th>已提现金额:</th>
    <td>{{$member->yesPayBillAmount()}}</td>
</tr>

<tr>
    <th>最后登录时间:</th>
    <td>{{$member->last_login_at}}</td>
</tr>
<tr>
    <th>注册日期:</th>
    <td>{{$member->reg_date}}</td>
</tr>

<tr>
    <th>状态:</th>
    <td>{{$member->statusItem($member->status)}}</td>
</tr>
<tr>
    <th>修改时间:</th>
    <td>{{$member->updated_at}}</td>
</tr>
<tr>
    <th>创建时间:</th>
    <td>{{$member->created_at}}</td>
</tr>
