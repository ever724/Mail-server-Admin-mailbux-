<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts._favicons')
    @include('layouts._css')

    <style>
        .gradient-custom {
            /* fallback for old browsers */
            background: #11BBCBFF;

            /* Chrome 10-25, Safari 5.1-6 */
            background: -webkit-linear-gradient(to right, rgb(17, 187, 203), rgba(37, 117, 252, 1));

            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            background: linear-gradient(to right, rgb(17, 187, 203), rgba(37, 117, 252, 1))
        }
    </style>
</head>

<body>
<nav class="navbar navbar-light bg-light w-100 position-absolute">
    <a class="navbar-brand" href="#">
        <i class="fas fa-lock"></i>
        <img src="{{get_system_setting('application_logo')}}" height="30" class="d-inline-block align-top"
             alt="{{get_system_setting('application_name')}}">
    </a>
</nav>
<section class="gradient-custom vh-100">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-sm-12 col-md-5 container">
                @yield('left-side')
            </div>
            <div class="col-12 col-sm-12 col-md-7">
                <div class="card" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <div class="checkout-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

@include('layouts._js')
@include('layouts._flash')
</body>
<script src="https://cdn.paddle.com/paddle/paddle.js"></script>
@if((int)get_system_setting('paddle_sandbox') == 1)
    <script type="text/javascript">
        Paddle.Environment.set('sandbox');
    </script>
@endif
@yield('custom_js')
</html>
