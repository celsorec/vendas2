<?php

require_once 'core/Database/DataRecord.php';
require_once 'core/Auth/AuthHelper.php';

$data = filter_input_array(INPUT_POST, FILTER_SANITIZE_ADD_SLASHES);
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
        //Informações da loja selecionada
        $connect   = new DataRecord();
        $dataStore = $connect->read(['nofem'], 'empre');

        //Logotipo
        $logo = explode(':', $_SERVER['HTTP_HOST'])[0];
        $logo = explode('.', $logo)[0];
        $logo = explode('_', $logo)[0];

        $_SESSION['logos'] = strtolower(str_replace(' ', '', $logo)); //Para pegar um ou outro logotipo
        $_SESSION['nofem'] = $dataStore[0]['nofem']; //Nome fantasia da loja
        $_SESSION['nomve'] = trim(substr($info[0]['nomve'], 0, -1)); //Remove espaços em branco e letra N
        $_SESSION['codve'] = $info[0]['codve'];
        $_SESSION['login'] = true;

        header("Location: index.php?view=home");
        exit;
    }
}
//Se login falhar
header("Location: " . $_SERVER['HTTP_REFERER']);