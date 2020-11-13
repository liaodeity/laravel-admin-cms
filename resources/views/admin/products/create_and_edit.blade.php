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
                    <form id="form-iframe-add" class="form-horizontal" action="{{$action_url ?? ''}}"
                          onsubmit="return false;">
                        @method($method ?? '')

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>商品标题</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Product[title]" maxlength="50"
                                       value="{{$product->title}}" id="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"> <span
                                    class="text-red">*</span>商品分类</label>

                            <div class="col-sm-10">
                                <select name="Product[cate_id]" id="" class="form-control">
                                    @foreach($cates as $cate)
                                        <option value="{{$cate->id}}"
                                                @if(isset($product->cate_id) && $product->cate_id == $cate->id) selected @endif>{{$cate->cate_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>产品型号</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Product[model]" maxlength="50"
                                       value="{{$product->model}}" id="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>产品单位</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Product[unit]" maxlength="20"
                                       value="{{$product->unit}}" id="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>产品仓库</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Product[warehouse]" maxlength="20"
                                       value="{{$product->warehouse}}" id="" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>执行标准</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Product[standard_no]" maxlength="50"
                                       value="{{$product->standard_no}}" id=""
                                       placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>保质期</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="Product[shelf_life]" maxlength="50"
                                       value="{{$product->shelf_life}}" id=""
                                       placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>商品规格</label>
                            <div class="col-sm-10">
                                <table id="guige" class="table table-bordered text-center">
                                    <thead>
                                    <tr>
                                        <th><span
                                                class="text-red">*</span>规格名称
                                        </th>
                                        <th><span
                                                class="text-red">*</span>单价
                                        </th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @forelse($product->prices as $key => $price)
                                        <tr>
                                            <td><input class="form-control" name="ProductPrice[specification][]" maxlength="50"
                                                       type="text" value="{{$price->specification}}"></td>
                                            <td><input class="form-control" name="ProductPrice[price][]" type="text" onkeyup="value=keyupNumber(this.value,2,999999.99)"
                                                       value="{{$price->price}}"></td>
                                            <td>
                                                @if($key> 0)
                                                    <button class="btn btn-danger" onclick="item_remove(this)">删除
                                                    </button>
                                                @else
                                                    <button class="btn btn-info" onclick="item_add()">增加</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td><input class="form-control"  name="ProductPrice[specification][]" maxlength="20" type="text"></td>
                                            <td><input class="form-control"  name="ProductPrice[price][]"  onkeyup="value=keyupNumber(this.value,2,999999.99)" type="text"></td>
                                            <td>
                                                <button class="btn btn-info" onclick="item_add()">增加</button>
                                            </td>
                                        </tr>

                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>商品内容</label>

                            <div class="col-sm-10">
                                <textarea id="content" name="Product[content]" style="display: none;">{!! $product->content ?? '' !!}</textarea>
                                <script  type="text/plain" id="myEditor" style="width:1000px;height:240px;">{!! $product->content !!}</script>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>商品视频</label>
                            <div class="col-sm-6">
                                <div class="input-group ">
                                    <input id="video_url" type="text" autocomplete="off" name="Product[video_url]" maxlength="220"
                                           value="{{$product->video_url}}"
                                           class="form-control width-100 -white">
                                    <span class="input-group-btn">
                                  <button id="filePicker" type="button" class="btn btn-default btn-flat">上传视频</button>
                                </span>
                                </div>
                                <span>（如有第三方外网链接地址http://或https://，可直接复制到输入框中，视频支持后缀名：mp4、webm、ogg）</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>卡片底色</label>
                            <div class="col-sm-2">
                                <div class="input-group ">
                                    <input id="my_color" type="text" autocomplete="off" name="Product[card_background]" maxlength="20"
                                           value="{{$product->card_background}}" class="form-control width-100 -white"
                                           style="background: {{$product->card_background}}">
                                    <span class="input-group-btn">
                                  <button type="button" class="btn btn-default btn-flat"
                                          onclick="javascipt:$('#my_color').val('').removeAttr('style');">清除</button>
                                </span>
                                </div>
                                <span>(格式要求：#000000或#000)</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>产品排序</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control " name="Product[sort]" onkeyup="value=keyupNumber(this.value,0)"
                                       value="{{$product->sort ?? 0}}" id="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red"></span>是否发展会员</label>
                            <div class="col-sm-2">
                                <div class="checkbox">
                                <label><input
                                        type="checkbox" name="Product[is_develop_member]" value="1" @if(isset($product->is_develop_member) && $product->is_develop_member == 1) checked @endif class="">是否发展会员</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"><span
                                    class="text-red">*</span>状态</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="radio">
                                        @foreach($product->statusItem() as $ind => $item)
                                            @if($ind >= 0)
                                                <label><input name="Product[status]"
                                                              @if(isset($product->status) && $product->status == $ind) checked
                                                              @endif  value="{{$ind}}" type="radio">{{$item}}</label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" onclick="UM.getEditor('myEditor').blur();" class="btn btn-info width-100 margin-top-15" data-confirm="确认保存？">
                                    保
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
    <script type="text/javascript">
        $ (function () {
            $ ('#my_color').iColor ({'x': 10, 'y': -50});
            window.UMEDITOR_CONFIG.imageUrl = '{{url('upload/image/upfile')}}?_token={{csrf_token()}}';
            window.UMEDITOR_CONFIG.imagePath = '';
            var um = UM.getEditor ('myEditor');
            um.ready(function(){
                um.addListener("blur",function(){
                    var content=UM.getEditor('myEditor').getContent();
                    document.getElementById('content').value = content;
                })
            });
        })

        function item_add () {
            $ ("#guige").append ('<tr>\n' +
                '                                    <td><input name="ProductPrice[specification][]" class="form-control" type="text"></td>\n' +
                '                                    <td><input name="ProductPrice[price][]" class="form-control" type="text"  onkeyup="value=keyupNumber(this.value,2,999999.99)"></td>\n' +
                '                                    <td><button class="btn btn-danger" onclick="item_remove(this)">删除</button></td>\n' +
                '                                </tr>');
        }

        function item_remove (obj) {
            $ (obj).parents ('tr').remove ()
        }

        var UPLOAD_URL = '/upload/annex/annex';
        var _TOKEN = '{{csrf_token()}}'
        // 初始化Web Uploader
        var uploader = WebUploader.create ({

            // 选完文件后，是否自动上传。
            auto: true,

            // swf文件路径
            swf: '{{asset ('admin-ui/lib/webuploader/Uploader.swf')}}',
            formData: {
                _token: _TOKEN,
                work_id: '0'
            },
            // 文件接收服务端。
            server: UPLOAD_URL,

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#filePicker',
            fileVal: 'annex',
            // 只允许选择图片文件。
            accept: {
                title: 'Files',
                extensions: 'mp4,webm,ogg'
            }
        });
        uploader.on ('uploadSuccess', function (file, data) {
            if (data.state == 'SUCCESS') {
                $ ("#video_url").val (data.url);
                top.layer.msg ('上传成功', {icon: 1, time: 2000, shade: 0.3});
            } else {

                top.layer.msg (data.info, {icon: 2, time: 2000, shade: 0.3});
            }
            uploader.reset ();
        });
        uploader.option ('compress', {
            width: 750,
            height: 1000
        });
        layer.photos ({
            photos: '.showImages',
            anim: 5
        });
    </script>
@endsection
