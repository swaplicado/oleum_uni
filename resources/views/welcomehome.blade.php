<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/uvaeth_aeth.png') }}">
        <title>{{ env('APP_NAME', false) }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #1D2F41;
                color: #f6f9fa;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #64B8D7;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Inicio</a>
                    @else
                        <a href="{{ route('login') }}">Iniciar sesión</a>

                        {{-- @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif --}}
                    @endauth
                </div>
            @endif

            <div class="content">
                <div>
                    {{-- <img src="{{ asset('img/logo.jpg') }}" alt=""> --}}
                    <img src="{{ asset('img/aeth_logo.png') }}" width="60%" height="60%" alt="">
                </div>
                <div>
                    <h2>Bienvenido a tu</h2>
                </div>
                <div class="title m-b-md">
                    Universidad Virtual AETH
                </div>
                <div>
                    <p style="font-size: 150%"><b>En AETH estamos comprometidos con tu desarrollo</b></p>
                </div>
                <br>
                {{-- <iframe src="https://drive.google.com/uc?export=preview&id=1gZgtYsgIdUfu-1JEiEVvMRvuJABYrD8Z" width="640" height="480" allow="autoplay"></iframe> --}}
                <div class="links">
                    <a href="#">AETH</a>
                    <a href="#">Grupo Tron Hermanos</a>
                    <a href="#">SIIE 3.2</a>
                    <a href="#">Blog</a>
                    <a href="#">Sistemas</a>
                    <a href="#">Software Aplicado</a>
                    <a href="#">Contacto</a>
                </div>
            </div>
        </div>
    </body>
</html>
