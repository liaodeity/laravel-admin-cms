<div class="layui-form-item">
    <label class="layui-form-label">登录账号 <span class="color-red">*</span></label>
    <div class="layui-input-inline ">
        <input type="text" class="layui-input " name="User[name]" maxlength="50" value="{{$user->name ?? ''}}" >
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">电子邮件 <span class="color-red"></span></label>
    <div class="layui-input-inline ">
        <input type="text" class="layui-input " maxlength="50" name="User[email]" value="{{$user->email ?? ''}}" >
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">用户密码 <span class="color-red"></span></label>
    <div class="layui-input-inline ">
        <input type="password" class="layui-input " maxlength="64" name="User[password]" value="" >
    </div>
    <div class="layui-form-mid layui-word-aux ">如为空，表示不修改密码</div>
</div>
