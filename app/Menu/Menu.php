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
                    (object) ['route' => route('profile'), 'icon' => 'bx bx-user', 'name' => 'Mi perfil'],
                    (object) ['route' => route('home'), 'icon' => 'bx bx-grid-alt', 'name' => 'Inicio'],
                    (object) ['route' => route('areas.index'), 'icon' => 'bx bx-library', 'name' => 'Mis áreas de competencia'],
                    (object) ['route' => route('home'), 'icon' => 'bx bxs-school', 'name' => 'Cursos'],
                    (object) ['route' => route('home'), 'icon' => 'bx bx-coin-stack', 'name' => 'Mis recompensas'],
                ];
                break;

            //Administrador
            case '2':
                $lMenus = [
                    (object) ['route' => route('profile'), 'icon' => 'bx bx-user', 'name' => 'Mi perfil'],
                    (object) ['route' => route('home'), 'icon' => 'bx bx-grid-alt', 'name' => 'Inicio'],
                    (object) ['route' => route('contents.index'), 'icon' => 'bx bx-movie-play', 'name' => 'Contenidos'],
                    (object) ['route' => route('kareas.index'), 'icon' => 'bx bx-area', 'name' => 'Gestión de áreas'],
                    (object) ['route' => route('assignments.index'), 'icon' => 'bx bxs-user-detail', 'name' => 'Asignar áreas'],
                    (object) ['route' => route('areas.index'), 'icon' => 'bx bx-library', 'name' => 'Mis áreas de competencia']
                ];
                break;

            //Administrador Sistema
            case '3':
                $lMenus = [
                    (object) ['route' => route('home'), 'icon' => 'bx bx-user', 'name' => 'Mi perfil'],
                    (object) ['route' => route('home'), 'icon' => 'bx bx-grid-alt', 'name' => 'Inicio'],
                ];
                break;

            //GH
            case '4':
                $lMenus = [
                    (object) ['route' => route('home'), 'icon' => 'bx bx-user', 'name' => 'Mi perfil'],
                    (object) ['route' => route('home'), 'icon' => 'bx bx-grid-alt', 'name' => 'Inicio'],
                ];
                break;
            
            default:
                $lMenus = [];
                break;
        }

        $sMenu = "";
        foreach ($lMenus as $menu) {
            $sMenu = $sMenu.Menu::createMenuElement($menu->route, $menu->icon, $menu->name);
        }

        return $sMenu;
    }

    private static function createMenuElement($route, $icon, $name)
    {
        return '<a href="'.$route.'" class="nav_link">
                    <i class="'.$icon.' nav_icon"></i> 
                    <span class="nav_name">'.$name.'</span>
                </a>';
    }
}