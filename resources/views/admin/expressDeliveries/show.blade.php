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
                                <th style="width:20%">快递名称:</th>
                                <td>{{$expressDelivery->name}}</td>
                            </tr>
                            <tr>
                                <th style="width:20%">快递接口标识:</th>
                                <td>{{$expressDelivery->com_code}}</td>
                            </tr>
                            <tr>
                                <th>排序:</th>
                                <td>{{$expressDelivery->sort}}</td>
                            </tr>
                            <tr>
                                <th>修改时间:</th>
                                <td>{{$expressDelivery->created_at}}</td>
                            </tr>
                            <tr>
                                <th>创建时间:</th>
                                <td>{{$expressDelivery->updated_at}}</td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('footer')

@endsection
