<?php

require_once 'Core/Menus/MenuHelper.php';
require_once 'app/modules/aceus_items.php'; //Arquivo de configurações de acesso ao sistema

//MENU DE FUNÇÕES
$mainmenu = new MenuHelper($menuAllowed, 'topmenu'); //aceus_items.php
$mainmenu = $mainmenu->renderItems();

//MENU INDIVIDUAL LOGOUT
$bottomMenu = [
    'form_login&logout' => 'Logout'
];
$submenu = new MenuHelper($bottomMenu, 'submenu');
$submenu = $submenu->renderItems();

return $mainmenu.$submenu;