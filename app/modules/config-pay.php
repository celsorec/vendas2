<?php

//Definindo valor de MOVCR como 'S'
$data['movcr'] = '';
$movcrValid =
[
    'Cartão Débito',
    'Cartão Visanet',
    'Cartão Mastercard',
    'Cartão American Express',
    'Cartão Credishop',
    'Cartão Hipercard', 
    'Cartão Outros',
    'Cartão Cred Vip Debito',
    'Cartão Crédito'
];
if(in_array($data['movnc'], $movcrValid)) $data['movcr'] = 'S';

//Adicionar 'Letra' a trans conforme movnc.value
$movncLetters =                       //Mapeamento de letras para o campo 'trans'
[
    'A prazo'                 => '',  //Se pagar com cartão = O, C, V...?
    'A vista'                 => '',
    'Cartão Débito'           => 'D',
    'Cartão Crédito'          => 'C', //igual a Credishop e Cartão Cred Vip Debito
    'Cartão Fidelidade'       => 'F',
    'Cartão Visanet'          => 'V',
    'Cartão Mastercard'       => 'M',
    'Cartão American Express' => 'A',
    'Cartão Credishop'        => 'C',
    'Cartão Hipercard'        => 'H',
    'Cartão Cred Vip Debito'  => 'Y', //Na tabel de 2024 consta como 'C' igual ao Credishop
    'Cartão Outros'           => 'O',
    'Cheque a vista'          => 'Q',
    'Cheque pre'              => 'Z',
    'Orçamento'               => '',  //Obter 'LETRA' com Hélio
    'Avarias'                 => 'a',
    'Garantia-Assistencia'    => 'g',
    'Garantia-Devolução'      => 'G',
    'Produtos-Transferencia'  => 'i',
    'Transferencia'           => 'n'
];
//Índices e valores de movnc são iguais aos índices de movncLetters
$data['trans'] = $movncLetters[$data['movnc']] ?? '';

//Grava 'v' ou 'p' na coluna MOVVP, conforme a forma de pagamento
if($data['movnc'] === 'A prazo' || $data['movnc'] === 'Cartão Fidelidade') $data['movvp'] = 'p';
else $data['movvp'] = 'v';