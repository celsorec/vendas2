<?php

//Classes
require_once 'Core/Forms/FormHelper.php';
require_once 'Core/Database/DataRecord.php';
require_once 'Core/Message/MessageHelper.php';
require_once 'Core/Auth/AuthHelper.php';
require_once 'Core/Logger/LoggerDatabase.php';
require_once 'Core/Datagrid/GridHelper.php';

//Configurações
require_once 'config.php';
require_once 'head.php';
require_once 'app/modules/aceus_items.php'; //Arquivo de configurações de acesso ao sistema

//Instâncias para uso em página importadas
$connect = new DataRecord();
$auth    = new AuthHelper();

//Mensagens do sistema
if(isset($_SESSION['message'])) print MessageHelper::getMessage();

//Mantendo limpo ID para evitar gravações equivicadas; Ex.: Novos cadastros
if(isset($_SESSION['idUpdate'])) unset($_SESSION['idUpdate']);

//Animação 'Processo em Andamento...'
$load = require_once 'app/modules/load.php';
print $load;

//Se não logado, importar apenas formulário de login
if(!isset($_SESSION['mfran']) || $_SESSION['mfran'] !== true)
{
    require_once 'app/pages/form_login.php';
    exit;
}

//Evita acesso de usuário desativados e atualiza níveis de acesso dos usuários
$activeUser = $connect->read(['atius', 'aceus'], 'usuar', "WHERE codus='{$_SESSION['codus']}' AND SQL_DELETED='F'");
if(is_array($activeUser) && $activeUser[0]['atius'] === 'T')
{
    $_SESSION['aceus'] = $activeUser[0]['aceus'];
}
else
{
    AuthHelper::logout();
    MessageHelper::setMessage('Usuário não registrado', 'alert');
    header("Location: index.php");
    exit;
}

//Importando módulos
          require_once 'app/modules/pagination.php'; //Não salva em variável porque não retorna string
$header = require_once 'app/modules/header.php';
$menu   = require_once 'app/modules/menu.php';
$footer = require_once 'app/modules/footer.php';
$page   = ''; //Variável definida abaixo na importação de páginas

//Importando páginas
if(isset($_GET['page']) && !empty($_GET['page']))
{
    if(file_exists('app/pages/'.$_GET['page'].'.php'))
    {
        $page = require_once 'app/pages/'.$_GET['page'].'.php';
    }
    else
    {
        $page = require_once 'app/pages/404.php';
    }
}

?>
<header id="mainheader">
    <?=$header;?>
</header>
<div class="wrapper">
    <nav id="mainmenu"> <?=$menu;?> </nav>

    <main id="main">
        <div class="press">
            <?=$page;?>
        </div>
    </main>
</div>

<footer id="mainfooter">
    <?=$footer;?>
</footer>