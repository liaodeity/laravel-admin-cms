@extends('common.layouts')
@section('style')
{{--    <link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">--}}
{{--    <script src="https://unpkg.com/video.js/dist/video.min.js"></script>--}}
    <style type="text/css">
        .product-video {
            min-height: 550px;
        }
    </style>
@endsection

@section('content')

    <div class="main-content">
        <section class="content">
            <div class="box-body">
                <div class="box ">
                    <div class="box-header">
                        <h3 class="box-title">{{$product->title}}</h3>
                    </div>
                    @if($product->video_url)
                        <iframe class="product-video" frameborder="0" width="100%" height="100%" src="{{$product->video_url ?? ''}}" allowfullscreen ></iframe>
{{--                        <video--}}
{{--                            id="my-player"--}}
{{--                            class="video-js product-video"--}}
{{--                            controls--}}
{{--                            preload="auto"--}}
{{--                            poster="{{asset('admin-ui/images/video-bg.png')}}"--}}
{{--                            data-setup='{}'>--}}
{{--                            <source src="{{$product->video_url}}" type="video/{{trim(get_extension($product->video_url,'mp4'),'.')}}"></source>--}}
{{--                            <p class="vjs-no-js">--}}
{{--                                To view this video please enable JavaScript, and consider upgrading to a--}}
{{--                                web browser that--}}
{{--                                <a href="https://videojs.com/html5-video-support/" target="_blank">--}}
{{--                                    supports HTML5 video--}}
{{--                                </a>--}}
{{--                            </p>--}}
{{--                        </video>--}}
                    @else
                        <h3>没有视频</h3>
                    @endif
                </div>
            </div>
        </section>
    </div>

@endsection

@section('footer')

@endsection
