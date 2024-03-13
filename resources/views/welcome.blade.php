<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/uvaeth_aeth.png') }}">
        <title>{{ 'UVAETH' }}</title>
        
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        
        <!-- Styles -->
        <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <style>
            html, body {
                background-color: rgba(0,0,0,.03);
                color: black;
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
                color: #1173B0;
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
        <link rel="stylesheet" type="text/css" href="{{ asset('myapp/css/footer.css') }}" />
        <style>
            .footer-bs {
            background-color: #093A58;
            padding: 20px 40px;
            color: rgba(255, 255, 255, 1.00);
            margin-bottom: 20px;
            border-bottom-right-radius: 6px;
            border-top-left-radius: 0px;
            border-bottom-left-radius: 6px;
            }
            
            .footer-bs .footer-brand,
            .footer-bs .footer-nav,
            .footer-bs .footer-social,
            .footer-bs .footer-ns {
            padding: 10px 25px;
            }
            
            .footer-bs .footer-nav,
            .footer-bs .footer-social,
            .footer-bs .footer-ns {
            border-color: transparent;
            }
            
            .footer-bs .footer-brand h2 {
            margin: 0px 0px 10px;
            }
            
            .footer-bs .footer-brand p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.70);
            }
            
            .footer-bs .footer-nav ul.pages {
            list-style: none;
            padding: 0px;
            }
            
            .footer-bs .footer-nav ul.pages li {
            padding: 5px 0px;
            }
            
            .footer-bs .footer-nav ul.pages a {
            color: rgba(255, 255, 255, 1.00);
            font-weight: bold;
            text-transform: uppercase;
            }
            
            .footer-bs .footer-nav ul.pages a:hover {
            color: rgba(255, 255, 255, 0.80);
            text-decoration: none;
            }
            
            .footer-bs .footer-nav h4 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
            }
            
            .footer-bs .footer-nav ul.list {
            list-style: none;
            padding: 0px;
            }
            
            .footer-bs .footer-nav ul.list li {
            padding: 5px 0px;
            }
            
            .footer-bs .footer-nav ul.list a {
            color: rgba(255, 255, 255, 0.80);
            }
            
            .footer-bs .footer-nav ul.list a:hover {
            color: rgba(255, 255, 255, 0.60);
            text-decoration: none;
            }
            
            .footer-bs .footer-social ul {
            list-style: none;
            padding: 0px;
            }
            
            .footer-bs .footer-social h4 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 3px;
            }
            
            .footer-bs .footer-social li {
            padding: 5px 4px;
            }
            
            .footer-bs .footer-social a {
            color: rgba(255, 255, 255, 1.00);
            }
            
            .footer-bs .footer-social a:hover {
            color: rgba(255, 255, 255, 0.80);
            text-decoration: none;
            }
            
            .footer-bs .footer-ns h4 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
            }
            
            .footer-bs .footer-ns p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.70);
            }
            
            @media (min-width: 768px) {
            .footer-bs .footer-nav,
            .footer-bs .footer-social,
            .footer-bs .footer-ns {
            border-left: solid 1px rgba(255, 255, 255, 0.10);
            }
            }
        </style>

        <script type="text/javascript" src="{{ asset('jquery/jquery-3.6.0.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
            <div class="row">
                <div class="col-12">
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
                </div>
            </div>
            <br>
            <br>
            @endif

            <div class="content">
                <div class="row">
                    <div class="col-12">
                        <img src="{{ asset('img/uvaeth_logosf.png') }}" width="60%" alt="">
                    </div>
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
                <div class="row">
                    <div class="col-12">
                        <div class="links">
                            <a href="#">AETH</a>
                            <a href="#">Tutorial</a>
                            <a href="#">Blog</a>
                            <a href="#">Software Aplicado</a>
                            <a href="#">Contacto</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <footer class="footer-bs row" style="--bs-gutter-x: 0rem;">
        <div class="row">
            <div class="col-11 offset-1 col-md-2 offset-xl-3 col-xl-2">
                <img src="{{ asset('img/swaplicado.png') }}" width="80%" alt="">
            </div>
            <div class="col-11 offset-1 offset-md-0 col-md-6 offset-xl-0 col-md-7">
                <p>Copyright © Software Aplicado. Todos los derechos reservados 2021.</p>
            </div>
        </div>
    </footer>
</html>
