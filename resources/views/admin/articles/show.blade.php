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
                                <th style="width:20%">公告标题:</th>
                                <td>{{$article->title}}</td>
                            </tr>
                            <tr>
                                <th>发布来源：</th>
                                <td>{{$article->push_source}}</td>
                            </tr>
                            <tr>
                                <th>浏览次数：</th>
                                <td>{{$article->view_number}}</td>
                            </tr>
                            <tr>
                                <th>公告内容：</th>
                                <td>
                                    {!! $article->content !!}
                                </td>
                            </tr>
                            <tr>
                            <tr>
                                <th>状态:</th>
                                <td>{!! $article->statusItem($article->status, true) !!}</td>
                            </tr>
                            <tr>
                                <th>修改时间:</th>
                                <td>{{$article->updated_at}}</td>
                            </tr>
                            <tr>
                                <th>创建时间:</th>
                                <td>{{$article->created_at}}</td>
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
