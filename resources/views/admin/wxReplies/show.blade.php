@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="box-body">
                <div class="box box-primary">
                    <div class="table">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:20%">关键词:</th>
                                <td>{{$keywords ?? ''}}</td>
                            </tr>
                            <tr>
                                <th>匹配类型:</th>
                                <td>{{$wxReply->ifLikeItem($wxReply->if_like)}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">回复内容:</th>
                                <td>{{$wxReply->content ?? ''}}</td>
                            </tr>
                            <tr>
                                <th>关注订阅:</th>
                                <td>{{$wxReply->isSubscribeItem($wxReply->is_subscribe)}}</td>
                            </tr>
                            <tr>
                                <th>状态:</th>
                                <td>{{$wxReply->statusItem($wxReply->status)}}</td>
                            </tr>
                            <tr>
                                <th>修改时间:</th>
                                <td>{{$wxReply->updated_at}}</td>
                            </tr>
                            <tr>
                                <th>创建时间:</th>
                                <td>{{$wxReply->created_at}}</td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <!-- /.row -->
        </section>


    </div>
@endsection

@section('footer')

@endsection
