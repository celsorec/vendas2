<?php

class Connect
{
    private $pdo;

    public function __construct($dbname)
    {   
        $host = '127.0.0.1';
        $base = $dbname;
        $user = '';
        $pass = '';

        try
        {
            //Conexão PDO
            $this->pdo = new PDO("mysql:host={$host};dbname={$base};charset=utf8;", $user, $pass);

            //Define o fuso horário da sessão do MySQL
            $this->pdo->exec("SET time_zone = '-03:00'");
    
            //Ativa mensagens de erro
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            die("Erro crítico na conexão com o banco de dados:" . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
