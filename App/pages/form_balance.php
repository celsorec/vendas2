<?php

//Para acionar método update na process.php
$idUpdate = filter_input(INPUT_GET, 'idUpdate', FILTER_VALIDATE_INT);

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 16;                   //Permite novos cadastros
if(($idUpdate)) $aceusPageId = 17;   //Evita edição/exclusão não autorizadas
aceusAllowed($aceusPageId);

//Para acionar método update na process.php
$_SESSION['idUpdate']  = $idUpdate ?? '';
$_SESSION['tableName'] = 'saldo';

//Buscando dados a partir do idUpdate
$columns = ['codsa', 'perio', 'saldo', 'filia', 'usuca', 'usuat', 'dtcad', 'dtatu', 'sql_rowid'];
if(!empty($idUpdate))
{
    $linkDelete = "process.php?idDelete=$idUpdate";
    $dataItems  = $connect->read($columns, 'saldo', "WHERE sql_rowid='$idUpdate' AND SQL_DELETED='F'");

    if(!is_array($dataItems))
    {
        MessageHelper::setMessage('Informação não localizada', 'alert');
        $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
        header("Location: index.php?page=".$linkPage);
        exit;
    }
}

//Dados para preencher para edição
foreach($columns as $var) $values[$var] = $dataItems[0][$var] ?? $_SESSION['dataForm'][$var] ?? null;
extract($values);

//Formulário solicitação de compras
$form = new FormHelper('process.php', 'id="form_request"');

$form->addInput('Ano', 'perio', 'text', "value='{$perio}' placeholder='0000' pattern='[0-9]{4}' required", 'col-3');
$form->addInput('Saldo', 'saldo', 'number', "value='{$saldo}' step='0.01' placeholder='0,00' required", 'col-3');

$filiaItems = FormHelper::getSelect(['sql_rowid', 'filialnome'], 'bancodados.bancosfil', "WHERE SQL_DELETED='F'"); //Pegando informações das filiais (centro de custos) de banco de dados externo
foreach($filiaItems as $key => $value) $filiaItems[$key] = trim(substr($value, 0, 49)); //Removendo 'S' e espaços em branco dos nomes das filiais; 'S' e espaços em branco são do banco de dados
$form->addSelect('Filial', 'filia', $filiaItems, 'required', "{$filia}", 'col-3');

//Informações complementares para itens cadastrados
if($codsa)
{
    require_once('app/modules/add_info.php');
    $addInfo = addInfo($connect, $codsa, $usuca, $usuat, $dtcad, $dtatu);
    $form->addHtml($addInfo);
}

//Renderização da página
$output = "<div id='form_request' class='page'><header class='mainheader'><h2>Saldo Orçamentário</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Saldo</span></a>" : '';
$output .= "</header>";
$output .= $form->renderForm();
$output .= "</div>";

return $output;