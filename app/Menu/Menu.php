<?php

namespace App\Menu;

class Menu {

    public static function createMenu($oUser = null)
    {
        if ($oUser == null) {
            return "";
        }

        switch ($oUser->user_type_id) {
            //Estándar
            case '1':
                $lMenus = [
                    // (object) ['route' => route('profile'), 'icon' => 'bx bx-user', 'name' => 'Mi perfil'],
                    (object) ['route' => route('kardex.index'), 'icon' => 'bx bxs-graduation', 'name' => 'Mi avance'],
                    (object) ['route' => route('areas.index'), 'icon' => 'bx bx-library', 'name' => 'Mis cuadrantes'],
                    (object) ['route' => route('kardex.head'), 'icon' => 'bx bxs-school', 'name' => 'Avance general'],
                    env('IS_STORE_ENABLED') ? ((object) ['route' => route('shop'), 'icon' => 'bx bxs-store', 'name' => 'Tienda']) : null,
                ];
                break;

            //Administrador
            case '2':
                $lMenus = [
                    // (object) ['route' => route('profile'), 'icon' => 'bx bx-user', 'name' => 'Mi perfil'],
                    // (object) ['route' => route('areas.index'), 'icon' => 'bx bx-grid-alt', 'name' => 'Mis cuadrantes'],
                    // (object) ['route' => route('kardex.index'), 'icon' => 'bx bxs-graduation', 'name' => 'Mi avance'],
                    // (object) ['route' => route('contents.index'), 'icon' => 'bx bx-movie-play', 'name' => 'Contenidos'],
                    // (object) ['route' => route('kareas.index'), 'icon' => 'bx bx-area', 'name' => 'Gestión de cuadrante'],
                    // (object) ['route' => route('assignments.index'), 'icon' => 'bx bxs-user-detail', 'name' => 'Asignar cuadrante'],
                    // (object) ['route' => route('assignments.scheduled.index'), 'icon' => 'bx bxs-calendar-event', 'name' => 'Programadas'],
                    // (object) ['route' => route('kardex.head'), 'icon' => 'bx bxs-school', 'name' => 'Avance general'],
                    // (object) ['route' => route('shop'), 'icon' => 'bx bxs-store', 'name' => 'Premios'],
                ];
                break;

            //GH
            case '3':
                $lMenus = [
                    // (object) ['route' => route('home'), 'icon' => 'bx bx-user', 'name' => 'Mi perfil'],
                    (object) ['route' => route('areas.index'), 'icon' => 'bx bx-grid-alt', 'name' => 'Mis cuadrantes'],
                    (object) ['route' => route('kardex.index'), 'icon' => 'bx bxs-graduation', 'name' => 'Mi avance'],
                    (object) ['route' => route('contents.index'), 'icon' => 'bx bx-movie-play', 'name' => 'Contenidos'],
                    (object) ['route' => route('kareas.index'), 'icon' => 'bx bx-area', 'name' => 'Gestión cuadrantes'],
                    (object) ['route' => route('assignments.index'), 'icon' => 'bx bxs-user-detail', 'name' => 'Asignar cuadrantes'],
                    (object) ['route' => route('assignments.scheduled.index'), 'icon' => 'bx bxs-calendar-event', 'name' => 'Cuadrantes programados'],
                    (object) ['route' => route('kardex.head'), 'icon' => 'bx bxs-school', 'name' => 'Avance general'],
                    env('IS_STORE_ENABLED') ? ((object) ['route' => route('shop'), 'icon' => 'bx bxs-store', 'name' => 'Tienda']) : null,
                    (object) ['route' => route('gifts.index'), 'icon' => 'bx bxs-cabinet', 'name' => 'Gestión premios'],
                    (object) ['route' => route('points.index'), 'icon' => 'bx bx-money', 'name' => 'Ctrl Puntos'],
                    (object) ['route' => route('carousel.index'), 'icon' => 'bx bx-images', 'name' => 'Carrusel'],
                    (object) ['route' => route('Reports'), 'icon' => 'bx bxs-report', 'name' => 'Reportes'],
                ];
                break;

            //Administrador Sistema
            case '4':
                $lMenus = [
                    // (object) ['route' => route('profile'), 'icon' => 'bx bx-user', 'name' => 'Mi perfil'],
                    (object) ['route' => route('areas.index'), 'icon' => 'bx bx-grid-alt', 'name' => 'Mis cuadrantes'],
                    (object) ['route' => route('kardex.index'), 'icon' => 'bx bxs-graduation', 'name' => 'Mi avance'],
                    (object) ['route' => route('contents.index'), 'icon' => 'bx bx-movie-play', 'name' => 'Contenidos'],
                    (object) ['route' => route('kareas.index'), 'icon' => 'bx bx-area', 'name' => 'Gestión cuadrantes'],
                    (object) ['route' => route('assignments.index'), 'icon' => 'bx bxs-user-detail', 'name' => 'Asignar cuadrantes'],
                    (object) ['route' => route('assignments.scheduled.index'), 'icon' => 'bx bxs-calendar-event', 'name' => 'Cuadrantes programados'],
                    (object) ['route' => route('kardex.head'), 'icon' => 'bx bxs-school', 'name' => 'Avance general'],
                    env('IS_STORE_ENABLED') ? ((object) ['route' => route('shop'), 'icon' => 'bx bxs-store', 'name' => 'Tienda']) : null,
                    (object) ['route' => route('gifts.index'), 'icon' => 'bx bxs-cabinet', 'name' => 'Gestión premios'],
                    (object) ['route' => route('points.index'), 'icon' => 'bx bx-money', 'name' => 'Ctrl Puntos'],
                    (object) ['route' => route('carousel.index'), 'icon' => 'bx bx-images', 'name' => 'Carrusel'],
                    (object) ['route' => route('users'), 'icon' => 'bx bxs-user-account', 'name' => 'Usuarios'],
                    (object) ['route' => route('Reports'), 'icon' => 'bx bxs-report', 'name' => 'Reportes'],
                ];
                break;
            
            default:
                $lMenus = [];
                break;
        }

        $sMenu = "";
        foreach ($lMenus as $menu) {
            if ($menu == null) {
                continue;
            }
            $sMenu = $sMenu.Menu::createMenuElement($menu->route, $menu->icon, $menu->name);
        }

        return $sMenu;
    }

    private static function createMenuElement($route, $icon, $name)
    {
        return '<a href="'.$route.'" class="nav_link" title="'.$name.'">
                    <i class="'.$icon.' nav_icon"></i> 
                    <span class="nav_name">'.$name.'</span>
                </a>';
    }
}