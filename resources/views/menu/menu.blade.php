<nav class="nav">
    <div>
        <a href="#" class="nav_logo">
            <i class="bx bx-layer nav_logo-icon"></i>
            <span class="nav_logo-name">Uni AETH</span>
        </a>
        <div class="nav_list" style="width: 100%; height: 600px; overflow-y: auto;">
            <?php $menu = '<a href="#" class="nav_link active"> <i class="bx bx-grid-alt nav_icon"></i>
                <span class="nav_name">Dashboard</span> </a>
            <a href="#" class="nav_link"> <i class="bx bx-user nav_icon"></i> <span class="nav_name">Users</span> </a>
            <a href="#" class="nav_link"> <i class="bx bx-message-square-detail nav_icon"></i> <span
                    class="nav_name">Messages</span> </a> <a href="#" class="nav_link"> <i
                    class="bx bx-bookmark nav_icon"></i>
                <span class="nav_name">Bookmark</span> </a> <a href="#" class="nav_link"> <i
                    class="bx bx-folder nav_icon"></i>
                <span class="nav_name">Files</span> </a>
            <a href="#" class="nav_link"> <i class="bx bx-bar-chart-alt-2 nav_icon"></i> <span
                    class="nav_name">Stats</span>
            </a>';
            ?>
                {!! $menu !!}
            @auth
                <a href="{{ route('logout') }}" class="nav_link" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                    <i class="bx bx-log-out nav_icon"></i>
                    <span class="nav_name">Salir</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endauth
        </div>
    </div>
</nav>