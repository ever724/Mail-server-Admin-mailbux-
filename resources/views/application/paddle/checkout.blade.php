@extends('layouts.checkout')

@section('left-side')
    <h4 class="mb-4 text text-md-center text-white">Welcome, {{$client->name}}</h4>
    <h5 class="mt-2 mb-4 text text-md text-white">You are subscribing to {{$plan->name}}</h5>

    @foreach($plan->features as $feature)
        <h5 class="mb-2 text text-sm text-grey">{{$feature->label}}: {{$feature->value}}</h5>
    @endforeach

    <p class="text mt-4 text-white">
        @if($isMonthly)
            Monthly price: ${{number_format($plan->monthly_price, 2)}}
        @else
            Yearly price: ${{number_format($plan->annual_price)}}
        @endif
    </p>
    <p class="mt-4 text text-white">
        Next billing date:
        @if($plan->trial_days)
            {{now()->addDays($plan->trial_days)->format('M d, Y')}} </p>
    <p class="mt-4 text text-bold text-white">
        Free trial {{$plan->trial_days}}
        @else
            @if($isMonthly)
                {{now()->addMonth()->format('M d, Y')}}
            @else
                {{now()->addYear()->format('M d, Y')}}
            @endif
        @endif
    </p>
@endsection

@section('custom_js')
    <script type="text/javascript">
        Paddle.Setup({vendor: {{get_system_setting('paddle_vendor_id')}}});
        Paddle.Checkout.open({
            method: 'inline',
            product: {{$plan_id}},
            allowQuantity: false,
            disableLogout: true,
            frameTarget: 'checkout-container',
            frameStyle: 'width:100%; min-width:312px; background-color: transparent; border: none;',
            email: '{{$client->email}}',
            successCallback: function (response) {
                if (response.checkout.completed) {
                    location.href = '{{route('mailbux.payment.complete')}}?checkout=' + response.checkout.id
                }
            }
        });
    </script>
@endsection