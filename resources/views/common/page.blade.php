<div id="list-page" class=" card-footer clearfix">
    <div class="col-sm-5">
        <div class="info">共有 <span class="page-total">{{$total ?? 0}}</span> 条记录</div>
    </div>
    <div class="col-sm-7 no-margin page-nav">
        {!! $page ?? '' !!}
    </div>
</div>
