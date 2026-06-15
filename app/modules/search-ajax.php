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
    if($table == 'produ01')
    {
        function movProducts(string $movtp)
        {
            global $pdo;
            global $exercicio;
            global $q;

            //Limitando busca para códigos de barras incompletos
            if(strlen($q) < 8)
            {
                print json_encode(['minLenghtAlert' => 'Insira pelo menos 8 dígitos']);
                exit;
            }

            //Quando saída ou entrada de produtos
            ($movtp == 'S') ? $movcx = "AND movcx <> ''" : $movcx = '';

            //Localiza entrada/saida de produtos
            $movProd = $pdo->prepare("SELECT DISTINCT movfa, movpr FROM mov{$exercicio} WHERE movpr LIKE '".$q."%' AND movtp='{$movtp}' $movcx AND SQL_DELETED='F'");
            $movProd->execute();
            $movProd = $movProd->fetchAll(PDO::FETCH_ASSOC);

            //Localiza grade com base na movimentação 'movfa'
            $movfa = array_column($movProd, 'movfa');
            $gragr = [];
            foreach($movfa as $key => $value)
            {
                $statement = $pdo->prepare("SELECT docgr, progr, gragr, quagr FROM grd{$exercicio} WHERE progr LIKE '".$q."%' AND docgr='{$value}' AND tipgr='{$movtp}' AND SQL_DELETED='F'");
                $statement->execute();
                $gragr[] = $statement->fetchAll(PDO::FETCH_ASSOC);
            }

            //Removendo duplicados; consta mesmo docgr para a mesma gragr mais de 1x
            $result = [];
            foreach($gragr as $key => $value)
            {
                $unique = [];
                foreach($value as $idx => $item)
                {
                    $index = $item['docgr'].'_'.$item['gragr'];
                    if(!isset($unique[$index]))
                    {
                        $unique[$index] = true;
                        $result[$key][$idx] = $item;
                    }
                }
            }
            $gragr = $result;

            //Simplificando resultado das buscas das grades            
            $quagr = [];
            foreach($gragr as $key => $value)
            {
                foreach($value as $idx => $item) $quagr[][$item['gragr']] = $item['quagr'];
            }

            //Somar quantidade de acordo com a grade
            $progr = [];
            foreach($quagr as $key => $value)
            {
                foreach($value as $idx => $item)
                {
                    //Se a grade já está no array, soma valores
                    if(array_key_exists($idx, $progr)) $progr[$idx] = $item + $progr[$idx];
                    //Se a grade NÃO está no array, atribui valor a ela e a adiciona
                    else $progr[$idx] = $item;
                }
            }
            return $progr;
        }

        $entProducts = movProducts('E');  //Estrada de produtos
        $saiProducts = movProducts('S');  //Saída de produtos

        //Calculando estoque
        $stokProducts = [];
        foreach($entProducts as $key => $value)
        {
            if(isset($saiProducts[$key])) $stokProducts[$key] = (string) ($value - $saiProducts[$key]);
            else $stokProducts[$key] = $value;
        }

        //Buscando informações principais do produto
        $columns = 'codpr, nompr, promo, venpr, prcpr';
        $produ01 = $pdo->prepare("SELECT $columns FROM $table WHERE codpr LIKE '".$q."%' AND SQL_DELETED='F'");
        $produ01->execute();
        $produ01 = $produ01->fetchAll(PDO::FETCH_ASSOC);

        $prodFinal = [];
        foreach($stokProducts as $key => $value)
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

            //Definindo estoque e grade
            $item['gragr'] = $key;
            $item['quagr'] = (int) $value;

            //Informação completa do produto
            $prodFinal[] = $item;
        }
        print json_encode($prodFinal);
    }
}