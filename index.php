<?php

//Classes
require_once 'Core/Database/DataRecord.php';
require_once 'Core/Message/MessageHelper.php';
require_once 'Core/Auth/AuthHelper.php';

//Configurações
require_once 'settings.php';
require_once 'app/modules/head.php';

//Mensagens do sistema
if(isset($_SESSION['message'])) print MessageHelper::getMessage();

//Animação 'Processo em Andamento...'
$load = require_once 'app/modules/load.php';
$menu = require_once 'app/modules/menu.php';

//Se não logado, importar apenas formulário de login
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true)
{
    require_once 'app/views/config.php';
    exit;
}

//Importando páginas
if(isset($_GET['view']) && !empty($_GET['view']))
{
    if(file_exists('app/views/'.$_GET['view'].'.php'))
    {
        $page = require_once 'app/views/'.$_GET['view'].'.php';
    }
    else
    {
        $page = require_once 'app/views/404.php';
    }
}
else
{
    require_once 'app/views/config.php';
}