<?php

class AuthHelper
{
    private $data;
    
    public function __construct()
    {
        $this->data = new DataRecord();
    }

    public function login(string $codus, string $senus): void
    {
        $result = $this->data->read(['codus', 'nomus', 'senus', 'aceus', 'filus', 'tipus'], 'usuar', "WHERE codus='{$codus}' AND atius='T' AND SQL_DELETED='F'");
        if(is_array($result) && password_verify($senus, $result[0]['senus']))
        {
            $_SESSION['mfran'] = true;
            $_SESSION['codus'] = $result[0]['codus'];
            $_SESSION['nomus'] = $result[0]['nomus'];
            $_SESSION['aceus'] = $result[0]['aceus'];
            $_SESSION['filus'] = $result[0]['filus'];
            $_SESSION['tipus'] = $result[0]['tipus'];
        }
    }

    public static function logout(): void
    {
        session_destroy();
        session_start();
    }
}