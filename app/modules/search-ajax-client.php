<?php

session_start();
header('Content-Type: application/json');

$host = '127.0.0.1';
$base = $_SESSION['dbname'];
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host={$host};dbname={$base};charset=utf8;", $user, $pass);
$q   = isset($_GET['q']) ? trim($_GET['q']) : '';

//Referências aos nomes das tabelas do banco de dados
$statement = $pdo->prepare("SELECT exerc, codem FROM empre");
$statement->execute();
$statement = $statement->fetch(PDO::FETCH_ASSOC);
$exercicio = $statement['exerc'].$statement['codem'];

//Tabelas da dados
$table = $_GET['table'];

if($q !== '')
{
    if($table == 'clien01')
    {
        //Exige mínimo de 3 dígitos
        if(strlen($q) < 3)
        {
            print json_encode(['minLenghtAlert' => 'Insira pelo menos 3 dígitos']);
            exit;
        }

        //Localizando cliente pelo código
        $columns = 'codcl, nomcl';
        $clien01 = $pdo->prepare("SELECT $columns FROM $table WHERE codcl LIKE '%".$q."%' AND SQL_DELETED='F' LIMIT 5");
        $clien01->execute();
        $clien01 = $clien01->fetchAll(PDO::FETCH_ASSOC);
        
        //Enviando informações via JSON
        print json_encode($clien01);   
    }
}