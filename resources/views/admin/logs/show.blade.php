@extends('common.layouts')
@section('style')

@endsection

@section('content')
    <div class="main-content">
        <section class="content">
            @if($json_detail)
                <pre>{!! $json_detail !!}</pre>
            @endif
        </section>
    </div>
@endsection

@section('footer')

@endsection
