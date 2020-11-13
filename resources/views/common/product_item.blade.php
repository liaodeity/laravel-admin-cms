@foreach($product->prices as $key => $price)
    <tr>
        @if($key === 0)
            <td rowspan="{{$count}}"><input class="check-item" type="checkbox" value="{{$product->id}}"></td>
            <td rowspan="{{$count}}"
                style="vertical-align:middle">{{$product->cate->cate_name ?? ''}}</td>
            <td rowspan="{{$count}}" style="vertical-align:middle">{{$product->title}}</td>
            <td rowspan="{{$count}}" style="vertical-align:middle">{{$product->model}}</td>
        @endif
        <td class="border-right-width-1px">{{$price->specification}}</td>
        <td class="border-right-width-1px">{{$price->price}}</td>

        @if($key === 0)
            <td rowspan="{{$count}}" style="vertical-align:middle">{{$product->standard_no}}</td>
            <td rowspan="{{$count}}">
                <small class="label  width-100" style="background: {{$product->card_background}} !important;">&nbsp;
                </small>
            </td>
                <td rowspan="{{$count}}">
                    {!! $product->isDevelopMemberItem($product->is_develop_member, true) !!}
                </td>
            <td rowspan="{{$count}}">
                {!! $product->statusItem($product->status, true) !!}
            </td>
            <td rowspan="{{$count}}">
                <div class="btn-group">
                    {!! $button !!}
                </div>
            </td>
        @endif
    </tr>
@endforeach
