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
                                <th style="width:20%">分类名称:</th>
                                <td>{{$productCate->cate_name}}</td>
                            </tr>
                            <tr>
                                <th>发布产品数：</th>
                                <td></td>
                            </tr>
                            <tr>
                            <tr>
                                <th>状态:</th>
                                <td>{{$productCate->statusItem($productCate->status)}}</td>
                            </tr>
                            <tr>
                                <th>修改时间:</th>
                                <td>{{$productCate->updated_at}}</td>
                            </tr>
                            <tr>
                                <th>创建时间:</th>
                                <td>{{$productCate->created_at}}</td>
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
