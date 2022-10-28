@extends('layouts.checkout')
@section('left-side')
    <div class="col-12 col-sm-12 col-md-5 container">
        <h4 class="mb-4 text text-md text-white">Welcome, {{$client->name}}</h4>
        <p class="text text-white">
            Monthly price: ${{number_format($plan->price, 2)}}
        </p>
        <p class="mt-4 text text-white">
            Next billing date:
            @if($plan->trial_days)
                {{now()->addDays($plan->trial_days)->format('M d, Y')}} </p>
        <p class="mt-4 text text-bold text-white">
            Free trial {{$plan->trial_days}}
            @else
                {{now()->addMonth()->format('M d, Y')}}
            @endif
        </p>

    </div>
@endsection
@section('custom_js')
    <script type="text/javascript">
        Paddle.Setup({vendor: {{get_system_setting('paddle_vendor_id')}}});
        Paddle.Checkout.open({
            override: '{{$update_url}}',
            method: 'inline',
            frameTarget: 'checkout-container',
            frameInitialHeight: 416,
            frameStyle: 'width:100%; min-width:312px; background-color: transparent; border: none;',
            success: '{{$success_url}}'
        });
    </script>
@endsection
