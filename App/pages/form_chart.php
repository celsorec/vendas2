<?php

//Para acionar método update na process.php
$idUpdate = filter_input(INPUT_GET, 'idUpdate', FILTER_VALIDATE_INT);

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 8;                   //Permite novos cadastros
if(($idUpdate)) $aceusPageId = 9;   //Evita edição/exclusão não autorizadas
aceusAllowed($aceusPageId);

//Para acionar método update na process.php
$_SESSION['idUpdate']  = $idUpdate ?? '';
$_SESSION['tableName'] = 'conta';

//Buscando dados a partir do idUpdate
$columns = ['clapl', 'nompl', 'idpai', 'tippl', 'natpl', 'usuca', 'usuat', 'dtcad', 'dtatu', 'sql_rowid'];
if(!empty($idUpdate))
{
    $linkDelete = "process.php?idDelete=$idUpdate";
    $dataItems  = $connect->read($columns, 'conta', "WHERE sql_rowid='$idUpdate' AND SQL_DELETED='F'");

    if(!is_array($dataItems))
    {
        MessageHelper::setMessage('Item não localizado', 'alert');
        $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
        header("Location: index.php?page=".$linkPage);
        exit;
    }
}

//Dados para preencher para edição
foreach($columns as $var) $values[$var] = $dataItems[0][$var] ?? $_SESSION['dataForm'][$var] ?? null;
extract($values);

if($idpai)
{
    $idpai = $connect->read(['sql_rowid', 'nompl'], 'conta', "WHERE sql_rowid='$idpai' AND SQL_DELETED='F'");
    $idpai = $idpai[0]['sql_rowid'].' - '.$idpai[0]['nompl'];
}

//Formulário categorias
$form = new FormHelper('process.php');
$form->addInput('Nome', 'nompl', 'text', "value='{$nompl}' placeholder='Nome descritivo' minlength='5' required", 'col-3');
$form->addInput('Classificação', 'clapl', 'text', "value='{$clapl}' placeholder='1.1.01' required", 'col-3');

$dataFilters = '
data-class="conta_field"
data-table="conta"
data-likes="clapl, nompl"
data-columns="clapl, nompl, sql_rowid"
data-filter="tippl,S"';
$form->addInput('Conta Superior', 'idpai', 'text', "value='{$idpai}' pattern='^\d+\s*-\s*.+$' placeholder='Buscar'", 'col-3 search', $dataFilters);

//Tipo
$tipplItems = ['S' => 'Sintética', 'A' => 'Analítica'];
$form->addSelect('Categoria', 'tippl', $tipplItems, 'required', "{$tippl}", 'col-3');

//Natureza
$natplItems = ['A' => 'Ativo (Patrimonial)', 'P' => 'Passivo (Patrimonial)', 'R' => 'Receitas (Resultados)', 'D' => 'Despesas (Resultados)'];
$form->addSelect('Natureza', 'natpl', $natplItems, 'required', "{$natpl}", 'col-3');

//Informações complementares para itens cadastrados
if($sql_rowid)
{
    require_once('app/modules/add_info.php');
    $addInfo = addInfo($connect, $clapl, $usuca, $usuat, $dtcad, $dtatu);
    $form->addHtml($addInfo);
}

//Renderização da página
$output = "<div id='register_client' class='page'><header class='mainheader'><h2>Cadastro de Plano de Contas</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Plano</span></a>" : '';
$output .= "</header>";
$output .= $form->renderForm();
$output .= "</div>";

return $output;