@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">基本信息</h3>

                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body">
                    <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? '' }}" onsubmit="return false;">
                        @method($method ?? '')
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>关键字</label>

                            <div id="keyword_div" class="col-sm-4">

                                @forelse($wxReply->keywords as $key => $item)
                                <div class="input-group margin-top-5">
                                    <input type="text" class="form-control" name="keywords[]" value="{{$item->keyword ?? ''}}" placeholder="" autocomplete="off">
                                    <span class="input-group-btn">
                                        @if($key == 0)
                                            <button class="btn btn-info" onclick="item_select()"><span class="fa fa-plus"></span>新增</button>
                                            @else
                                            <button class="btn btn-danger" onclick="javascript:$(this).parents('.input-group').remove()"><span class="fa  fa-times-circle-o"></span>删除</button>
                                        @endif
                                    </span>
                                </div>
                                    @empty
                                        <div class="input-group margin-top-5">
                                            <input type="text" class="form-control" name="keywords[]" value="" placeholder="" autocomplete="off">
                                            <span class="input-group-btn">
                                        <button class="btn btn-info" onclick="item_select()"><span class="fa fa-plus"></span>新增</button>
                                    </span>
                                        </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>匹配类型</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($wxReply->ifLikeItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="WxReply[if_like]" @if(isset($wxReply->if_like) && $wxReply->if_like == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>回复内容</label>

                            <div class="col-sm-10">
                                <textarea class="form-control" name="WxReply[content]" autocomplete="off" cols="30" maxlength="5000" rows="5">{{$wxReply->content ?? ''}}</textarea>
                                链接效果示例：<pre>&lt;a href='{{url('/')}}'&gt;点击注册&lt;/a&gt;</pre>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>是否关注订阅回复</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($wxReply->isSubscribeItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="WxReply[is_subscribe]" @if(isset($wxReply->is_subscribe) && $wxReply->is_subscribe == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($wxReply->statusItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="WxReply[status]" @if(isset($wxReply->status) && $wxReply->status == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info width-100 margin-top-15">保
                                    存
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('footer')
    <script>
        function item_select() {
            html = '<div class="input-group margin-top-5">\n' +
                '                                    <input type="text" class="form-control" name="keywords[]" id="" value="" placeholder="" autocomplete="off">\n' +
                '                                    <span class="input-group-btn">\n' +
                '                                        <button class="btn btn-danger" onclick="javascript:$(this).parents(\'.input-group\').remove()"><span class="fa  fa-times-circle-o"></span>删除</button>\n' +
                '                                    </span>\n' +
                '                                </div>';
            $("#keyword_div").append(html)
        }
    </script>
@endsection
