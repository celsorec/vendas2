<?php

//Gerenciamento de sessões
if(session_status() === PHP_SESSION_NONE)
{
    $sessionPath = __DIR__.'/../../sessions';

    if(!is_dir($sessionPath))
    {
        mkdir($sessionPath, 0777, true);

        //Leitura e Escrita no Windows
        if(PHP_OS_FAMILY === 'Windows') exec('icacls "' . $sessionPath . '" /grant IIS_IUSRS:(OI)(CI)M');
    }

    session_save_path($sessionPath);
    session_start();
}

require_once __DIR__.'/../Database/DataRecord.php';
require_once __DIR__.'/../Message/MessageHelper.php';

class AuthHelper
{
    private $data;

    public function __construct()
    {
        $this->data = new DataRecord();
    }

    public function login(string $codve)
    {
        $result = $this->data->read(['codve', 'nomve'], 'vencr', "WHERE codve='$codve' AND sql_deleted='F'");
        if(is_array($result))
        {
            MessageHelper::setMessage('Bem-vindo!', 'success');
            return $result;
        }
        else
        {
            MessageHelper::setMessage('Usuário não cadastrado', 'alert');
            return false;
        }
    }

    public function logout()
    {
        MessageHelper::setMessage('Você está desconectado', 'info');
        $_SESSION = [];
        session_destroy();
    }
}