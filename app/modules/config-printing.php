<?php

/**
 * MONTAGEM DO COMPROVANTE DE VENDAS
 */
$prdto = ''; //Informações da venda
$movde = 0;  //Para calcular valor original sem desconto
$tProd = 0;  //Total de itens vendidos
foreach($data['codpr'] as $key => $prod)
{
    $prdto .= substr($prod.' '.$data['nompr'][$key], 0, 40) . PHP_EOL . '    ';                              //Código e nome do produto
    $prdto .= str_pad($data['movqt'][$key], 4, ' ', STR_PAD_BOTH) . '|';                                     //Quantidade
    $prdto .= str_pad($data['gragr'][$key], 7, ' ', STR_PAD_BOTH) . ' |';                                    //Grade
    $prdto .= str_pad(number_format($data['venpr'][$key], 2), 11, ' ', STR_PAD_LEFT) . ' |';                 //Valor unitário
    $prdto .= str_pad(number_format($data['venpr'][$key] * $data['movqt'][$key], 2), 13, ' ', STR_PAD_LEFT); //Subtotal (qtd * preço)
    if($key+1 < count($data['codpr'])) $prdto .= PHP_EOL . '    ';
    
    $movde += $data['venpr'][$key] * $data['movqt'][$key]; //Cálculo do valor original sem desconto
    $tProd += $data['movqt'][$key];                        //Total de itens vendidos
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
    date('d/m/Y'),                                                               //Data da venda
    date('H:i:s'),                                                               //Hora da venda
    str_pad(substr($data['movnc'], 0, 17), 17, ' '),                             //Forma de pagamento
    substr($data['codcl'], 0, 32),                                               //Cliente
    str_pad(number_format($movde, 2), 13, ' '),                                  //Valor total da venda
    str_pad($data['movip'].'%', 12, ' ', STR_PAD_BOTH),                          //Percentual de desconto
    str_pad($data['desco'], 13, ' ', STR_PAD_LEFT),                              //Valor final com desconto
    str_pad(number_format(((float) $data['fentr']), 2), 11, ' '),                //Valor da entrada
    str_pad($data['fnpre'].'x de '.$data['fcalc'], 14, ' ', STR_PAD_BOTH),       //Quantidade e valores das parcelas
    str_pad($data['ftota'], 13, ' ', STR_PAD_LEFT),                              //Total da parcela
    $tProd,                                                                      //Total de itens comprados para conferênia
    str_pad($movfa, 10, ' '),                                                    //Número da venda
    str_pad($_SESSION['codve'].' | '.$_SESSION['nomve'], 20, ' ', STR_PAD_LEFT)  //Vendedor
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

if(!file_put_contents($printFilePath, $comprovante))
{
    MessageHelper::setMessage('Erro ao salvar o comprovante para impressão', 'alert');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}