<nav class="nav">
    <div>
        <a href="{{ route('home') }}" class="nav_logo">
            <i class='bx bxs-home nav_logo-icon'></i>
            <span class="nav_logo-name">{{ env('APP_NAME', false) }}</span>
        </a>
        {{-- <a href="{{ route('home') }}" class="nav_logo">
            <img src="{{ asset('img/uvaeth_logo.jpg') }}" width="80%" height="80%" alt="">
        </a> --}}
        <div class="nav_list" style="width: 100%; height: 600px; overflow-y: auto;">
            {!! session()->has('menu') ? session('menu') : "" !!}
            {{-- @auth
                <a class="nav_link" title="Salir" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bx bx-log-out nav_icon"></i>
                    <span class="nav_name">Salir</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endauth --}}
        </div>
    </div>
</nav>