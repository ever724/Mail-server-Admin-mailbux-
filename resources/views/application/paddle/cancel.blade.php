@extends('layouts.checkout')

@section('left-side')
    <h4 class="mb-4 text text-md text-white">Welcome, {{$client->name}}</h4>
@endsection

@section('custom_js')
    <script type="text/javascript">
        Paddle.Setup({vendor: {{get_system_setting('paddle_vendor_id')}}});
        Paddle.Checkout.open({
            override: '{{$cancel_url}}',
            method: 'inline',
            frameTarget: 'checkout-container',
            frameInitialHeight: 416,
            frameStyle: 'width:100%; min-width:312px; background-color: transparent; border: none;',
            success: '{{$success_url}}'
        });
    </script>
@endsection
