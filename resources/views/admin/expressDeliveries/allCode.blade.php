@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            <div class="box-body">
                <div class="box box-primary">
                    <div class="table">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th style="width:20%">快递名称:</th>
                                <th>快递标识</th>
                            </tr>
                            @foreach($allCode as $code => $name)
                            <tr>
                                <td>{{$name}}</td>
                                <td>{{$code}}</td>
{{--                                <td>'{{$code}}',//{{$name}}</td>--}}
                            </tr>
                            @endforeach
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
