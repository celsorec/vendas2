<?php

$logout = filter_input(INPUT_GET,  'logout', FILTER_SANITIZE_SPECIAL_CHARS);
$codus  = filter_input(INPUT_POST, 'codus',  FILTER_SANITIZE_SPECIAL_CHARS);
$senus  = filter_input(INPUT_POST, 'senus',  FILTER_SANITIZE_SPECIAL_CHARS);

if(isset($logout)) //Logout
{
    AuthHelper::logout();
    MessageHelper::setMessage('Você está desconectado', 'info');
    header("Location: index.php");
    exit;
}
else //Login
{
    if(isset($codus) && !empty($codus))
    {
        $auth->login($codus, $senus);
        if(!isset($_SESSION['mfran']))
        {
            MessageHelper::setMessage('Usuário não cadastrado', 'alert');
        }
        else
        {
            MessageHelper::setMessage('Bem-vindo!', 'success');
        }
        header("Location: index.php");
    }
}

//Evitando apresentar formulário de login (vindo via $_GET) caso usuário esteja logado
if(isset($_SESSION['mfran']) && $_SESSION['mfran'] === true)
{
    header("Location: index.php");
    exit;
}

//Formulário de login
$formUser = new FormHelper('index.php', "id='login'", '', 'Entrar');
$formUser->addInput('Código', 'codus', 'text', "placeholder='Informe seu código' inputmode='numeric' minlength='4' required", 'col-12');
$formUser->addInput('Senha', 'senus', 'password', "placeholder='Digite sua senha' minlength='8' required", 'col-12');

//Renderização da página
$output  = "<div id='form-login'><header><h2>Login</h2></header>";
$output .= $formUser->renderForm();
$output .= "</div>";

print $output;