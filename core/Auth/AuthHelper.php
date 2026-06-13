<?php

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