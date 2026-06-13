<?php

require_once 'core/Database/DataRecord.php';
require_once 'core/Auth/AuthHelper.php';

$data = filter_input_array(INPUT_POST, FILTER_SANITIZE_ADD_SLASHES);
if($data) $_SESSION['dbname'] = $data['location']; //Nome do banco de dados
$auth = new AuthHelper();

//Logout
$logout = filter_input_array(INPUT_GET, FILTER_SANITIZE_ADD_SLASHES);
if(isset($logout['logout']))
{
    $auth->logout();
    header("Location: index.php");
    exit;
}
else
{   //Login
    $info = $auth->login($data['usercode']);

    if(is_array($info))
    {
        $_SESSION['logos'] = strtolower(str_replace(' ', '', $data['store'])); //Para pegar um ou outro logotipo
        $_SESSION['store'] = $data['store']; //Nome da loja
        $_SESSION['login'] = true;
        $_SESSION['nomve'] = trim(substr($info[0]['nomve'], 0, -1)); //Remove espaços em branco e letra N
        header("Location: index.php?view=home");
        exit;
    }
}
//Se login falhar
header("Location: " . $_SERVER['HTTP_REFERER']);