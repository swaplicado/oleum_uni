<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Uni GH</title>
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('myapp/css/mycss.css') }}" rel="stylesheet">
    <link href="{{ asset('swal2/css/sweetalert2.css') }}" rel="stylesheet">
    <link href="{{ asset('select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('datatables/datatables.min.css') }}" rel="stylesheet">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('datatables/DataTables-1.10.25/css/dataTables.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/AutoFill-2.3.7/css/autoFill.bootstrap5.css') }}"/> --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/Buttons-1.7.1/css/buttons.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/ColReorder-1.5.4/css/colReorder.bootstrap5.min.css') }}"/>
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('datatables/DateTime-1.1.0/css/dataTables.dateTime.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/FixedColumns-3.3.3/css/fixedColumns.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/FixedHeader-3.1.9/css/fixedHeader.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/KeyTable-2.6.2/css/keyTable.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/Responsive-2.2.9/css/responsive.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/RowGroup-1.1.3/css/rowGroup.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/RowReorder-1.2.8/css/rowReorder.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/Scroller-2.0.4/css/scroller.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/SearchBuilder-1.1.0/css/searchBuilder.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/SearchPanes-1.3.0/css/searchPanes.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/Select-1.3.3/css/select.bootstrap5.min.css') }}"/> --}}
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap");

        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --first-color: #4723D9;
            --first-color-light: #AFA5D9;
            --white-color: rgb(240, 245, 247);
            --body-font: 'Nunito', sans-serif;
            --normal-font-size: 1rem;
            --z-fixed: 100
        }

        *,
        ::before,
        ::after {
            box-sizing: border-box
        }

        body {
            position: relative;
            margin: var(--header-height) 0 0 0;
            padding: 0 1rem;
            font-family: var(--body-font);
            font-size: var(--normal-font-size);
            transition: .5s
        }

        a {
            text-decoration: none
        }

        .header {
            width: 100%;
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background-color: var(--white-color);
            z-index: var(--z-fixed);
            transition: .5s
        }

        .header_toggle {
            color: var(--first-color);
            font-size: 1.5rem;
            cursor: pointer
        }

        .header_img {
            width: 30px;
            height: 35px;
            justify-content: center;
            border-radius: 50%;
            /* overflow: hidden; */
        }

        .header_img img {
            width: 40px
        }

        .header_usrname {
            width: 35px;
            height: 35px;
            justify-content: center;
        }

        .l-navbar {
            position: fixed;
            top: 0;
            left: -30%;
            width: var(--nav-width);
            height: 100vh;
            background-color: var(--first-color);
            padding: .5rem 1rem 0 0;
            transition: .5s;
            z-index: var(--z-fixed)
        }

        .nav {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden
        }

        .nav_logo,
        .nav_link {
            display: grid;
            grid-template-columns: max-content max-content;
            align-items: center;
            column-gap: 1rem;
            padding: .5rem 0 .5rem 1.5rem
        }

        .nav_logo {
            margin-bottom: 2rem
        }

        .nav_logo-icon {
            font-size: 1.25rem;
            color: var(--white-color)
        }

        .nav_logo-name {
            color: var(--white-color);
            font-weight: 700
        }

        .nav_link {
            position: relative;
            color: var(--first-color-light);
            margin-bottom: 1.5rem;
            transition: .3s
        }

        .nav_link:hover {
            color: var(--white-color)
        }

        .nav_icon {
            font-size: 1.25rem
        }

        .showm {
            left: 0
        }

        .body-pd {
            padding-left: calc(var(--nav-width) + 1rem)
        }

        .active {
            color: var(--white-color)
        }

        .active::before {
            content: '';
            position: absolute;
            left: 0;
            width: 2px;
            height: 32px;
            background-color: var(--white-color)
        }

        .height-100 {
            height: 90vh
        }

        @media screen and (min-width: 768px) {
            body {
                margin: calc(var(--header-height) + 1rem) 0 0 0;
                padding-left: calc(var(--nav-width) + 2rem)
            }

            .header {
                height: calc(var(--header-height) + 1rem);
                padding: 0 2rem 0 calc(var(--nav-width) + 2rem)
            }

            .header_img {
                width: 30px;
                height: 40px
            }

            .header_img img {
                width: 35px
            }

            .l-navbar {
                left: 0;
                padding: 1rem 1rem 0 0
            }

            .showm {
                width: calc(var(--nav-width) + 156px)
            }

            .body-pd {
                padding-left: calc(var(--nav-width) + 188px)
            }
        }
    </style>
    @yield('css_section')
    <script type="text/javascript" src="{{ asset('jquery/jquery-3.6.0.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vue/vue.js') }}"></script>
    <script type="text/javascript" src="{{ asset('axios/axios.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('swal2/js/sweetalert2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/myjs.js') }}"></script>
    <script type="text/javascript" src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/JSZip-2.5.0/jszip.min.js') }}"></script>
    {{-- <script type="text/javascript" src="{{ asset('datatables/pdfmake-0.1.36/pdfmake.min.js') }}"></script> --}}
    <script type="text/javascript" src="{{ asset('datatables/pdfmake-0.1.36/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/DataTables-1.10.25/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/AutoFill-2.3.7/js/dataTables.autoFill.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/Buttons-1.7.1/js/dataTables.buttons.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/Buttons-1.7.1/js/buttons.colVis.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/Buttons-1.7.1/js/buttons.html5.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/Buttons-1.7.1/js/buttons.print.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/ColReorder-1.5.4/js/dataTables.colReorder.min.js') }}"></script>
    @yield('scripts_section')
    @yield('scripts_section_complement')
</head>

<body class="snippet-body" id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class="bx bx-menu" id="header-toggle"></i> </div>
        <div class="row">
            <div class="header_img col">
                <img src="https://i.imgur.com/hczKIze.jpg" alt="">
            </div>
            <div class="col">
                @guest
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                @else
                    <button class="btn btn-light ms-3">{{ Auth::user()->username }}</button>
                @endguest
            </div>
        </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        @include('menu.menu')
    </div>
    <!--Container Main start-->
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        @yield('content_title', 'Unknow')
                    </div>
    
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Container Main end-->
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
    
    const showNavbar = (toggleId, navId, bodyId, headerId) =>{
    const toggle = document.getElementById(toggleId),
    nav = document.getElementById(navId),
    bodypd = document.getElementById(bodyId),
    headerpd = document.getElementById(headerId)
    
    // Validate that all variables exist
    if(toggle && nav && bodypd && headerpd){
    toggle.addEventListener('click', ()=>{
    // show navbar
    nav.classList.toggle('showm')
    // change icon
    toggle.classList.toggle('bx-x')
    // add padding to body
    bodypd.classList.toggle('body-pd')
    // add padding to header
    headerpd.classList.toggle('body-pd')
    })
    }
    }
    
    showNavbar('header-toggle','nav-bar','body-pd','header')
    
    /*===== LINK ACTIVE =====*/
    const linkColor = document.querySelectorAll('.nav_link')
    
    function colorLink(){
    if(linkColor){
    linkColor.forEach(l=> l.classList.remove('active'))
    this.classList.add('active')
    }
    }
    linkColor.forEach(l=> l.addEventListener('click', colorLink))
    
    // Your code to run since DOM is loaded and ready
    });
    </script>
    @yield('bottom_scripts')
</body>

</html>