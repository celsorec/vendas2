<?php
//Classes
require_once 'Core/Database/DataRecord.php';
require_once 'Core/Message/MessageHelper.php';
require_once 'Core/Auth/AuthHelper.php';

//Configurações
require_once 'settings.php';
require_once 'app/modules/head.php';
require_once 'localconnection.php';

//Mensagens do sistema
if(isset($_SESSION['message'])) print MessageHelper::getMessage();

//Animação 'Processo em Andamento...'
$load = require_once 'app/modules/load.php';
$menu = require_once 'app/modules/menu.php';

//Se não estiver conectado à mesma rede do servidor
if(!$accessAllowed)
{
    $page = require_once 'app/views/403.php';
    exit;
}

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
    $page = require_once 'app/views/home.php';
}
?>

<!-- PWA -->
<script>
if ('serviceWorker' in navigator) {
	window.addEventListener('load', () => {
		// Registra definindo o escopo exato para a sua subpasta
		navigator.serviceWorker.register('./sw.js', { scope: './' })
		.then((registration) => {
			console.log('Service Worker registrado com escopo:', registration.scope);
		})
		.catch((error) => {
			console.log('Falha ao registrar:', error);
		});
	});
}
</script>