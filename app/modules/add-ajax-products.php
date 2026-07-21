<?php

header('Content-Type: application/json');
require_once '../../Core/Message/MessageHelper.php';

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
    if($table == 'produ01')
    {
        //Buscando informações principais do produto; removendo trecho da grade na string
        $columns = 'codpr, nompr, promo, venpr, prcpr';
        $produ01 = $pdo->prepare("SELECT $columns FROM $table WHERE codpr='".substr($q, 0, -(strlen($q) - 8))."' AND SQL_DELETED='F'");
        $produ01->execute();
        $produ01 = $produ01->fetchAll(PDO::FETCH_ASSOC);

        $statement = $pdo->prepare("SELECT DISTINCT gragr FROM grd{$exercicio} WHERE progr='".substr($q, 0, -(strlen($q) - 8))."' AND gragr='".trim(substr($q, 8))."' AND SQL_DELETED='F'");
        $statement->execute();
        $gragr = $statement->fetchAll(PDO::FETCH_ASSOC);

        $prodFinal = [];
        foreach($gragr as $key => $value)
        {
            //Repetindo informações dos produtos
            $item['codpr'] =         $produ01[0]['codpr'];
            $item['nompr'] =         $produ01[0]['nompr'];
            $item['venpr'] =         $produ01[0]['venpr'];
            $item['prcpr'] =         $produ01[0]['prcpr'];
            $item['promo'] = (float) $produ01[0]['promo'];
            
            //Se existe preço promocional, remove preço padrão ou o contrário
            if($item['promo'] > 0) unset($item['venpr']);
            else unset($item['promo']);

            $item['movqt'] = (float) 1; //movqt não consta no cadastro de produtos; é informação para movYYYY
            $item['subtt'] = (float) ($item['promo'] ?? $item['venpr']); //subtt não consta no cadastro de produtos; é informação cálculo de subtotal

            //Definindo estoque e grade
            $item['gragr'] = $value['gragr'];
            $item['quagr'] = (int) $value;

            //Informação completa do produto
            $prodFinal[] = $item;
        }
        if(count($prodFinal) > 0) print json_encode($prodFinal[0]);
        else MessageHelper::setMessage('Produto não localizado. Tente novamente ou use a busca manual.', 'alert');
    }
}