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
                    <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? ''}}" onsubmit="return false;">
                        @method($method ?? '')
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>公告标题</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="" name="Article[title]" maxlength="100" value="{{$article->title}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>发布来源</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="" name="Article[push_source]" maxlength="50" value="{{$article->push_source}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>浏览次数</label>

                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="Article[view_number]" maxlength="10" onkeyup="value=keyupNumber(this.value,0)" value="{{$article->view_number}}" id="" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>公告内容</label>

                            <div class="col-sm-10">
                                <textarea id="content" name="Article[content]" style="display: none;">{!! $article->content ?? '' !!}</textarea>
                                <script type="text/plain" id="myEditor" style="width:1000px;height:240px;">{!! $article->content ?? '' !!}</script>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($article->statusItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="Article[status]" @if(isset($article->status) && $article->status == $ind) checked @endif  value="{{$ind}}" type="radio">{{$item}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" onclick="UM.getEditor('myEditor').blur();" class="btn btn-info width-100 margin-top-15" data-confirm="确认保存？">保
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
        $(function () {
            window.UMEDITOR_CONFIG.imageUrl = '{{url('upload/image/upfile')}}?_token={{csrf_token()}}';
            window.UMEDITOR_CONFIG.imagePath = '';
            var um = UM.getEditor('myEditor');
            um.ready(function(){
                um.addListener("blur",function(){
                    var content=UM.getEditor('myEditor').getContent();
                    document.getElementById('content').value = content;
                })

            });
        })
    </script>
@endsection
