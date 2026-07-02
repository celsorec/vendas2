<?php

require_once 'Database/DataRecord.php';
require_once 'config.php';
require_once 'Message/MessageHelper.php';

$pdo  = (new DataRecord)->getConnection();
$data = filter_input_array(INPUT_POST, FILTER_SANITIZE_ADD_SLASHES);

//Definindo data no servidor na hora da venda
$data['movda'] = date('Y-m-d');

//CONVERTENDO NOMES: NOME ORIGINAL VEM DA TABELA DE ORIGEM, NOVO NOME É PARA TABELA DE DESTINO
$data['movfo'] = $data['codcl']; //Cliente             -> string
$data['movpr'] = $data['codpr']; //Códigos do produto  -> array
$data['movct'] = $data['prcpr']; //Preços de custo (?) -> array
$data['movvc'] = $data['venpr']; //Preços de venda (?) -> array

//Removendo convertidos acima
unset($data['codcl']);
unset($data['codpr']);
unset($data['prcpr']);
unset($data['venpr']);

//Removendo campos informativos
unset($data['clien01']);   //Nome da tabela para busca ajax
unset($data['produ01']);   //Nome da tabela para busca ajax
unset($data['nompr']);     //Nome do produto; OBS: Lá na frente eu busco novamente, mas deveria ter aproveitado essa informação, não deveria tê-la removido
unset($data['subtotal']);  //Cálculo para cada produto; deveria ser removido?

//RENDER PRINT
$movpr = $data['movpr'];
$connect = new DataRecord();
$movfu = $connect->read('vencr', ['nomve'], "codve='".$data['movfu']."' AND SQL_DELETED='F'"); //Nome vendedor

//Referências aos nomes das tabelas do banco de dados
$empre = $connect->read('empre', ['exerc', 'codem']);
$empre = $empre[0]['exerc'] . $empre[0]['codem'];

//Nomes produtos pelo código
$nompr = [];
foreach($movpr as $codpr)
{
    $nompr[] = $connect->read('produ01', ['nompr'], "codpr='".$codpr."'");
}

//Simplificando array nomes produtos $nompr => $nomeProdct
$nomeProdct = [];
foreach($nompr as $value)
{
    $nomeProdct[] = $value[0]['nompr'];
}

//GRAVAÇÃO NO BANCO DE DADOS
try
{
    $pdo->beginTransaction(); //Iniciada a transação

    $movpr = $data['movpr']; // Array para controlar quantidade de inserções, conforme quantidade de produtos
    
    $data['movtb'] = $data['movvc'];
    $data['movlj'] = '01';
    
    //Código numbl para montar código do Faturamento (numbl . data['vende'])
    $movfa = new DataRecord;
    $movfa = $movfa->read('empre', ['numbl']);
    if(!$movfa || !isset($movfa[0]['numbl']))
    {
        throw new Exception('Não foi possível obter o número de faturamento.');
    }

    //Atualizando numbl na tabela empre
    $numbl = new DataRecord;
    $numbl = $numbl->update('empre', ['numbl' => $movfa[0]['numbl']+1], '1');
    if($numbl !== true)
    {
        throw new Exception('Não foi possível atualizar os dados da venda.');
    }
    
    //Buscando parâmetro para compoição de MOVFA (se MOVFA tem em sua composição o número do vendedor)
    $param = new DataRecord;
    $param = $param->read('param', ['param']);
    $param = $param[0]['param'][486];

    //Montagem do código do Faturamento com ou sem código do vendedor (MOVFA + CODVE)
    if($param === 'S') $movfa = ($movfa[0]['numbl']+1).$data['vende'];
    else $movfa = ($movfa[0]['numbl']+1); //Sem código do vendedor
    $data['movfa'] = str_repeat(' ', 10 - strlen($movfa)) . $movfa;

    //TABELA GRD_ANO
    $dataLoop = $data['movpr'];
    $dataGrade = [];
    foreach($dataLoop as $key => $value)
    {
        $infoGrade['progr'] = $data['movpr'][$key];
        $infoGrade['docgr'] = $data['movfa'];
        $infoGrade['gragr'] = $data['gragr'][$key];
        $infoGrade['quagr'] = $data['movqt'][$key];
        $infoGrade['tipgr'] = 'S';
    
        $dataGrade[] = $infoGrade;
    }
    
    $saveGrade = new DataRecord;
    foreach($dataGrade as $value)
    {
        $result = $saveGrade->save('grd'.$empre, $value);
        if ($result !== true)
        {
            throw new Exception('Falha ao salvar na tabela grd'.$empre.'.');
        }
    }
    //TABELA GRD_ANO
    
    //Campos nulos
    $data['movca'] = 0.00;
    $data['movdp'] = 0.00;
    $data['fjuro'] = 0.00;
    $data['fepre'] = 0.00;
    $data['fcoef'] = 0.00;
    $data['mcomp'] = 0.00;
    $data['mlarg'] = 0.00;
    $data['caxme'] = 0.00;
    
    //Percorrendo e separando cada produto para gravação em banco de dados
    $dataProducts = [];
    foreach($movpr as $index => $item)
    {
        $dataSave = [];
        foreach($data as $key => $value)
        {
            if(is_array($value))
            {
                //Converter movqt (qtd) para float para o banco de dados
                if($key == 'movqt')
                {
                    $dataSave[$key] = number_format($value[$index], 3);
                }
                else
                {
                    $dataSave[$key] = $value[$index];
                }
            }
            else
            {
                //Selecionado apenas o código do cliente para salvar no banco de dados
                if($key == 'movfo')
                {
                    $movfo = explode(' | ', $value,);
                    $movfo = $movfo[0];
    
                    $dataSave[$key] = $movfo;
                }
                else
                {
                    $dataSave[$key] = $value;
                }
            }    
        }
        $dataProducts[] = $dataSave;
    }

    //Somando quantidade de produtos que têm o mesmo código e portanto são o mesmo produto para a tabela movYYYY (mesmo com grades diferentes)
    $movprGroup = [];
    foreach($dataProducts as $value)
    {
        $movpr = $value['movpr'];
        
        //Se produto ainda não foi adicionado ao novo array, adiciona ele completo
        if(!isset($movprGroup[$movpr]))
        {
            $movprGroup[$movpr] = $value;
            $movprGroup[$movpr]['movqt'] = 0; //Zera quantidade para iniciar soma
        }
        
        //Acumula quantidade
        $movprGroup[$movpr]['movqt'] += $value['movqt'];
    }

    //Sobrescreve array original por produtos únicos e somados
    $dataProducts = array_values($movprGroup);

    //Salvando produtos no banco de dados
    foreach($dataProducts as $key => $value)
    {
        $tableName = array_key_first($value);
        array_shift($value);
        unset($value['gragr']);
    
        //Banco de dados
        $save = new DataRecord;
        $result = $save->save($tableName, $value);
        if($result !== true)
        {
            throw new Exception('Falha ao salvar produto.');
        }
    }

    //Commit
    $pdo->commit();
    MessageHelper::setMessage('Informações salvas com sucesso', 'success');
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
catch(Exception $e)
{
    if($pdo->inTransaction())
    {
        $pdo->rollBack();
    }
    MessageHelper::setMessage('ERRO: ' . $e->getMessage(), 'alert');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

/**
 * MONTAGEM DO COMPROVANTE DE VENDAS
 */

//Detalhes da venda
$prdto = ''; //Informações da venda
$movde = 0;  //Para calcular valor original sem desconto
$totalConfirm = 0;
foreach($data['movpr'] as $key => $prod)
{
    $prdto .= substr($prod.' '.$nomeProdct[$key], 0, 40) . PHP_EOL . '    ';                                 //Código e nome do produto
    $prdto .= str_pad($data['movqt'][$key], 4, ' ', STR_PAD_BOTH) . '|';                                     //Quantidade
    $prdto .= str_pad($data['gragr'][$key], 7, ' ', STR_PAD_BOTH) . ' |';                                    //Grade
    $prdto .= str_pad(number_format($data['movvc'][$key], 2), 11, ' ', STR_PAD_LEFT) . ' |';                 //Valor unitário
    $prdto .= str_pad(number_format($data['movvc'][$key] * $data['movqt'][$key], 2), 13, ' ', STR_PAD_LEFT); //Subtotal (qtd * preço)
    if($key+1 < count($data['movpr'])) $prdto .= PHP_EOL . '    ';

    //Cálculo do valor original sem desconto
    $movde += $data['movvc'][$key] * $data['movqt'][$key];

    //
    $totalConfirm += $data['movqt'][$key];
}

//Strings que serão substituídas no arquivo comprovante.txt
$search =
[
    '{__DATA__}',
    '{_HORA_}',
    '{FORMA_PAGAMENTO}',
    '{CLIENTE}',
    '{TOTAL_VENDA}',
    '{PERCENTUAL}',
    '{TOTAL_FINAL}',
    '{VLR_ENTRD}',
    '{PARCELAMENTO}',
    '{T_PARCELADO}',
    '{TOTAL_ITENS}',
    '{000000__}',
    '{_____VENDEDOR_____}'
];

//Informações que substituirão strings acima
$replace =
[
    date('d/m/Y'),                                                                                                //Data da venda
    date('H:i:s'),                                                                                                //Hora da venda
    str_pad(substr($data['movnc'], 0, 17), 17, ' '),                                                              //Forma de pagamento
    substr($data['movfo'], 0, 32),                                                                                //Cliente
    str_pad(number_format($movde, 2), 13, ' '),                                                                   //Valor total da venda
    str_pad($data['movip'].'%', 12, ' ', STR_PAD_BOTH),                                                           //Percentual de desconto
    str_pad(number_format($movde - (($movde / 100) * ((float) $data['movip'])), 2), 13, ' ', STR_PAD_LEFT),       //Valor final com desconto
    str_pad(number_format(((float) $data['fentr']), 2), 11, ' '),                                                 //Valor da entrada
    str_pad($data['fnpre'].'x de '.$data['fcalc'], 14, ' ', STR_PAD_BOTH),                                        //Quantidade e valores das parcelas
    str_pad($data['ftota'], 13, ' ', STR_PAD_LEFT),                                                               //Total da parcela
    $totalConfirm,                                                                                                //Total de itens comprados para conferênia
    str_pad($movfa, 10, ' '),                                                                                     //Número da venda
    str_pad(substr($data['movfu'].' | '.trim(substr($movfu[0]['nomve'], 0, -1)), 0, 22), 20, ' ', STR_PAD_LEFT)   //Vendedor
];

//Importando matriz do comprovante e inserindo informações reais
$comprovante = file_get_contents('comprovante.txt');
$comprovante = str_replace($search, $replace, $comprovante);
$comprovante = str_replace('{PRODUTO}', $prdto, $comprovante);

//Copiando comprovante para dupla impressão
$comprovante .= str_repeat(PHP_EOL, 5);   //Linhas em branco
$comprovante .= chr(27) . chr(109);       //ESC m - corte parcial
$comprovante .= str_repeat(PHP_EOL, 2);   //Linhas em branco
$comprovante .= $comprovante;

//IMPRESSÃO DO COMPROVANTE
//Caminho local da pasta compartilhada no servidor
$printFilePath = 'C:\\impressao_compartilhada\\comprovante_'.trim($data['movfa']).'_'.uniqid().'.txt';

if(file_put_contents($printFilePath, $comprovante))
{
    MessageHelper::setMessage('Comprovante enviado para impressão', 'success');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
else
{
    MessageHelper::setMessage('Erro ao salvar o comprovante para impressão', 'alert');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}