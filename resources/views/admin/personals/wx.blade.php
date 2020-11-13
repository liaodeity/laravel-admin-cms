@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content-header">
            <h1>
                账号管理
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">账号管理</li>
            </ol>
        </section>
        <section class="content">

            <div class="row">
                <div class="col-md-3">
                    @include('admin.personals._nav')
                </div>
                <div class="col-md-9">
                    <div class="box box-primary">
                        <div class="table">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th style="width:20%">管理员名称:</th>
                                    <td>{{$admin->nickname}}</td>
                                </tr>
                                <tr>
                                    <th style="width:20%">绑定微信:</th>
                                    <td>{{$admin->wxAccount->nickname ?? ''}}
                                        @if(isset($admin->wxAccount->id))
                                        <button id="unbindWx" class=" btn btn-xs btn-danger">解除绑定</button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:20%">绑定时间:</th>
                                    <td>{{$admin->wxAccount->created_at ?? ''}}</td>
                                </tr>
                                @if(!isset($admin->wxAccount->id))
                                <tr>
                                    <th>扫码绑定</th>
                                    <td>
                                        <p>
                                            <img id="qrcode" src="{{$qrcode_img}}"
                                                 alt="">
                                            <button id="get_new_code" class=" btn btn-xs btn-default" data-toggle="tooltip" data-placement="right" title="点击刷新二维码"><i class="fa fa-refresh" ></i></button></p>
                                        <p>1、该二维码有效期5分钟，请及时扫码，如过期请刷新</p>
                                        <p>2、请使用微信扫码二维码进行绑定，绑定成功后，请关注公众号</p>
                                    </td>
                                </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>

@endsection

@section('footer')
<script type="text/javascript">
    $(function () {
        $("#get_new_code").click(function () {
            $.ajax({
                type: 'post',
                url: '{{route ('personals.wx-new-code')}}',
                data: '',
                dataType: 'json',
                error: function () {
                    top.layer.msg('访问失败', {
                        icon: 2,
                        time: ERROR_TIP_TIME,
                        shade: 0.3
                    });
                },
                complete: function () {
                    // $("#get_new_code").removeClass('disabled').prop('disabled', false);
                },
                success: function (result) {
                    if (result.error !== true) {
                        $("#qrcode").attr('src',result.img)
                    } else {
                        top.layer.msg(result.message, {
                            icon: 5,
                            time: ERROR_TIP_TIME,
                            shade: 0.3
                        });
                    }
                }
            })
        })
        $("#unbindWx").click(function () {
            $("#unbindWx").addClass('disabled').prop('disabled', true);
            $.ajax({
                type: 'post',
                url: '{{route ('personals.wx-unbind')}}',
                data: '',
                dataType: 'json',
                error: function () {
                    top.layer.msg('访问失败', {
                        icon: 2,
                        time: ERROR_TIP_TIME,
                        shade: 0.3
                    });
                },
                complete: function () {
                    $("#unbindWx").removeClass('disabled').prop('disabled', false);
                },
                success: function (result) {
                    if (result.error !== true) {
                        top.layer.msg(result.message, {
                            icon: 1,
                            time: SUCCESS_TIP_TIME,
                            shade: 0.3
                        });
                        setTimeout(function () {
                            location.reload()
                        }, SUCCESS_TIP_TIME);

                    } else {
                        top.layer.msg(result.message, {
                            icon: 5,
                            time: ERROR_TIP_TIME,
                            shade: 0.3
                        });
                    }
                }
            })
        });
        @if(!isset($admin->wxAccount->id))
            check_wx_bind()
        @endif
    })
    function check_wx_bind(){
        $.ajax({
            type: 'post',
            url: '{{route ('personals.check-wx-bind')}}',
            data: '',
            dataType: 'json',
            error: function () {
            },
            complete: function () {
                setTimeout(function () {
                    check_wx_bind();
                },2000);

            },
            success: function (result) {
                if (result.error !== true) {
                    location.reload()

                }
            }
        })
    }
</script>
@endsection
