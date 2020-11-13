@foreach($product->prices as $key => $price)
    <tr>
        @if($key === 0)
            <td rowspan="{{$count}}" style="vertical-align:middle">{{$product->title}}</td>
            <td rowspan="{{$count}}" style="vertical-align:middle">{{$product->model}}</td>
            <td rowspan="{{$count}}" style="vertical-align:middle">{{$product->standard_no}}</td>
        @endif
        <td class="border-right-width-1px">{{$price->specification}}</td>
        <td class="border-right-width-1px">{{$price->price}}</td>
        @if($key === 0)
            <td rowspan="{{$count}}">
                <small class="label  width-100" style="background: {{$product->card_background}} !important;">&nbsp;
                </small>
            </td>
        @endif
    </tr>
@endforeach
