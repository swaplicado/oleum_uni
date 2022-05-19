<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME', false) }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/uvaeth_aeth.png') }}">
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('daterangepicker/css/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('myapp/css/mycss.css') }}" rel="stylesheet">
    <link href="{{ asset('swal2/css/sweetalert2.css') }}" rel="stylesheet">
    <link href="{{ asset('select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('datatables/datatables.min.css') }}" rel="stylesheet">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('datatables/DataTables-1.10.25/css/dataTables.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/AutoFill-2.3.7/css/autoFill.bootstrap5.css') }}" /> --}}
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/Buttons-1.7.1/css/buttons.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/ColReorder-1.5.4/css/colReorder.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('datatables/DateTime-1.1.0/css/dataTables.dateTime.min.css') }}"/>
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/Responsive-2.2.9/css/responsive.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/FixedColumns-3.3.3/css/fixedColumns.bootstrap5.min.css') }}" />
    {{-- <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/FixedHeader-3.1.9/css/fixedHeader.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/KeyTable-2.6.2/css/keyTable.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/RowGroup-1.1.3/css/rowGroup.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/RowReorder-1.2.8/css/rowReorder.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/Scroller-2.0.4/css/scroller.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/SearchBuilder-1.1.0/css/searchBuilder.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/SearchPanes-1.3.0/css/searchPanes.bootstrap5.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('datatables/Select-1.3.3/css/select.bootstrap5.min.css') }}" /> --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('myapp/css/uni.css') }}" />
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('myapp/css/uni2.css') }}" /> --}}
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('myapp/css/uni3.css') }}" /> --}}
    <style>
        /* Dropdown Button */
            .dropbtn {
                background-color: #64B8D7;
                color: #3d3d3d;
                /* padding: 16px;
                font-size: 16px;
                border: none; */
            }

            /* The container <div> - needed to position the dropdown content */
            .dropdown {
                position: relative;
                display: inline-block;
            }

            /* Dropdown Content (Hidden by Default) */
            .dropdown-content {
                display: none;
                position: absolute;
                background-color: #f1f1f1;
                min-width: 160px;
                box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                z-index: 1;
            }

            /* Links inside the dropdown */
            .dropdown-content a {
                color: black;
                padding: 12px 16px;
                text-decoration: none;
                display: block;
            }

            /* Change color of dropdown links on hover */
            .dropdown-content a:hover {background-color: #ddd;}

            /* Show the dropdown menu on hover */
            .dropdown:hover .dropdown-content {display: block;}

            /* Change the background color of the dropdown button when the dropdown content is shown */
            .dropdown:hover .dropbtn {background-color: #08004d;}

            @media only screen and (max-width: 1026px) {
                #hide-in-small {
                    display: none;
                }
            }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('myapp/css/footer.css') }}" />
    @yield('css_section')
    <script type="text/javascript" src="{{ asset('jquery/jquery-3.6.0.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('moment/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vue/vue.js') }}"></script>
    <script type="text/javascript" src="{{ asset('axios/axios.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('swal2/js/sweetalert2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('daterangepicker/js/daterangepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/myjs.js') }}"></script>
    <script type="text/javascript" src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/JSZip-2.5.0/jszip.min.js') }}"></script>
    {{-- <script type="text/javascript" src="{{ asset('datatables/pdfmake-0.1.36/pdfmake.min.js') }}"></script> --}}
    <script type="text/javascript" src="{{ asset('datatables/pdfmake-0.1.36/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/DataTables-1.10.25/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/Responsive-2.2.9/js/dataTables.responsive.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/AutoFill-2.3.7/js/dataTables.autoFill.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/Buttons-1.7.1/js/dataTables.buttons.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/Buttons-1.7.1/js/buttons.colVis.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/Buttons-1.7.1/js/buttons.html5.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/Buttons-1.7.1/js/buttons.print.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatables/ColReorder-1.5.4/js/dataTables.colReorder.min.js') }}">
    </script>
    <script type="text/javascript">
        moment.locale('es-mx');
    </script>
    @yield('scripts_section')
    @yield('scripts_section_complement')
</head>

<body class="snippet-body" id="body-pd">
    @if(session('message'))
        <script>
            msg = "<?php echo session('message'); ?>";
            myIcon = "<?php echo session('icon'); ?>"

            Swal.fire({
                icon: myIcon,
                title: msg
            })
        </script>
    @endif
    <header class="header" id="header">
        <div class="header_toggle">
            <div class="row">
                <div class="col">
                    <i class="bx bx-menu" id="header-toggle"></i>
                    {{-- <img src="{{ asset('img/uvaeth_black.jpg') }}" width="50%" height="110%"alt=""> --}}
                    {{-- <img src="{{ asset('img/uvaeth_black_sf.png') }}" width="50%" height="110%" alt=""> --}}
                </div>
                <div class="col">
                    {{-- <img src="{{ asset('img/aeth-header.png') }}" width="42px" height="40px" class="card-img-top" alt=""> --}}
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="header_img col">
                <a href="{{ route('profile') }}"><img src="{{ asset(\Auth::user()->profile_picture) }}" alt=""></a>
            </div>
            <div class="col"></div>
            <div class="col">
                @guest
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                @else
                    <div class="dropdown">
                        <button type="button" class="btn btn-primary dropbtn" data-toggle="dropdown">
                            {{ Auth::user()->username }}
                        </button>
                        <div class="dropdown-content">
                            <a href="{{ route('profile') }}">
                                <i class='bx bxs-user-circle'></i>
                                <span class="nav_name">Mi perfil</span>
                            </a>
                            <a class="nav_link" title="Salir" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bx bx-log-out nav_icon"></i>
                                <span class="nav_name">Salir</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
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
                        <div class="row">
                            <div class="col-9" style="white-space: nowrap;">
                                <h2><b style="color: #F7F6F6">@yield('content_title', 'Unknow')</b></h2>
                                <div>
                                    @yield('title_comp')
                                </div>
                            </div>
                            <div class="col-3" style="text-align: right;">
                                <div style="float: right;">
                                    @yield('right_header')
                                </div>
                                <img id="hide-in-small" style="text-align: right" src="{{ asset('img/uvaeth_black.jpg') }}" width="50%" height="100%" alt="">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
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
    <br>
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
@include('layouts.unifooter')
</html>