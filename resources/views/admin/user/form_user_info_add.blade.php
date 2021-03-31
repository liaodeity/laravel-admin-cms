<div class="layui-form-item">
    <label class="layui-form-label">真实名称 <span class="color-red"></span></label>
    <div class="layui-input-inline ">
        <input type="text" class="layui-input " maxlength="20" name="UserInfo[real_name]" value="{{$user->info->real_name ?? ''}}" >
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">性别</label>
    <div class="layui-input-block">
        @foreach(\App\Libs\Parameter::genderItem () as $ind => $item)
            <input type="radio" name="UserInfo[gender]" value="{{$ind}}" @if(isset($user->info->gender) && $user->info->gender == $ind) checked
                   @endif title="{{$item}}">

        @endforeach
    </div>
    <div class="layui-form-mid layui-word-aux "></div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">联系电话 <span class="color-red"></span></label>
    <div class="layui-input-inline ">
        <input type="text" class="layui-input " maxlength="20" name="UserInfo[telephone]" value="{{$user->info->telephone ?? ''}}" >
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">地址 <span class="color-red"></span></label>
    <div class="layui-input-inline ">
        <input type="text" class="layui-input " maxlength="100" name="UserInfo[address]" value="{{$user->info->address ?? ''}}" >
    </div>
</div>
