<div class="box box-primary">
    <div class="box-body box-profile">
        <a href="{{route('personals.show')}}"><img class="profile-user-img img-responsive img-circle"
                                         src="{{show_user_image($admin->wxAccount->headimgurl ?? '')}}" alt="User profile picture">
        </a>
        <h3 class="profile-username text-center">{{$admin->nickname}}</h3>

        <p class="text-muted text-center">加盟日期：{{$admin->created_at->format('Y-m-d')}}</p>

        <a href="{{route('personals.edit',$admin->id)}}" class="btn btn-default btn-block"><b>修改资料</b></a>
        <a href="{{route('personals.password')}}" class="btn btn-default btn-block"><b>修改密码</b></a>
        <a href="{{route('personals.wx')}}" class="btn btn-default btn-block"><b>绑定微信</b></a>
    </div>
    <!-- /.box-body -->
</div>
