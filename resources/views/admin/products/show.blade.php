@extends('common.layouts')
@section('style')

@endsection

@section('content')

    <div class="main-content">
        <section class="content">
            <div class="box-body">
                <div class="box box-primary">
                    <table class="table text-center table-bordered">
                        <thead>
                        <tr>
                            <th>商品名称</th>
                            <th>产品型号</th>
                            <th>标准</th>
                            <th>规格</th>
                            <th>单价</th>
                            <th>卡片底色</th>
                        </tr>
                        </thead>
                        <tbody>
                            {!! $product_html !!}
                        </tbody>
                    </table>
                    <div class="table">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:20%">分类名称:</th>
                                <td> {{$product->cate->cate_name ?? ''}}</td>
                            </tr>
                            <tr>
                                <th>商品视频:</th>
                                <td>
                                    {{$product->video_url}}
                                    @if($product->video_url)
                                    <button onclick="show_video_fun('查看商品视频','{{route('products.video',$product->id)}}')" class="btn btn-default">查看视频</button>
                                        @endif
                                </td>
                            </tr>
                            <tr>
                                <th>商品仓库:</th>
                                <td>{{$product->warehouse}}</td>
                            </tr>
                            <tr>
                                <th>商品单位:</th>
                                <td>{{$product->unit}}</td>
                            </tr>
                            <tr>
                                <th>产品内容：</th>
                                <td>
                                    {!! $product->content !!}
                                </td>
                            </tr>
                            <tr>
                                <th>是否发展会员:</th>
                                <td>{{$product->is_develop_member == 1 ? '是' : '否'}}</td>
                            </tr>
                            <tr>
                                <th>商品排序:</th>
                                <td>{{$product->sort}}</td>
                            </tr>
                            <tr>
                                <th>状态:</th>
                                <td>{{$product->statusItem($product->status)}}</td>
                            </tr>
                            <tr>
                                <th>修改时间:</th>
                                <td>{{$product->updated_at}}</td>
                            </tr>
                            <tr>
                                <th>创建时间:</th>
                                <td>{{$product->created_at}}</td>
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
