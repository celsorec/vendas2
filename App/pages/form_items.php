<?php

//Para acionar método update na process.php
$idUpdate = filter_input(INPUT_GET, 'idUpdate', FILTER_VALIDATE_INT);

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 3;                   //Permite novos cadastros
if(($idUpdate)) $aceusPageId = 4;   //Evita edição/exclusão não autorizadas
aceusAllowed($aceusPageId);

//Para acionar método update na process.php
$_SESSION['idUpdate']  = $idUpdate ?? '';
$_SESSION['tableName'] = 'itens';

//Buscando dados a partir do idUpdate
$columns = ['codit', 'nomit', 'decit', 'preit', 'tipit', 'plaid', 'usuca', 'usuat', 'dtcad', 'dtatu', 'sql_rowid'];
if(!empty($idUpdate))
{
    $linkDelete = "process.php?idDelete=$idUpdate";
    $dataItems  = $connect->read($columns, 'itens', "WHERE sql_rowid='$idUpdate' AND SQL_DELETED='F'");

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

if($plaid)
{
    $plaid = $connect->read(['contadre', 'nomecdre'], 'bancodados.contasdre', "WHERE contadre='$plaid'");
    $plaid = $plaid[0]['contadre'].' - '.$plaid[0]['nomecdre'];
}

//Formulário categorias
$form = new FormHelper('process.php');
$form->addInput('Nome', 'nomit', 'text', "value='{$nomit}' placeholder='Nome do item' minlength='5' required", 'col-3');
$form->addInput('Descrição', 'decit', 'text', "value='{$decit}' placeholder='Descrição do item' minlength='5' required", 'col-3');
$form->addInput('Preço', 'preit', 'number', "value='{$preit}' placeholder='0.00' step='0.01' required", 'col-3');

//Tipo
$tipitItems = ['P' => 'Produto', 'S' => 'Serviço'];
$form->addSelect('Tipo', 'tipit', $tipitItems, 'required', "{$tipit}", 'col-3');

//Plano de contas //codicont, nomecont
$dataFilters = '
data-class="itens_field"
data-dbname="bancodados"
data-table="contasdre"
data-limit="1"
data-likes="contadre, nomecdre"
data-columns="contadre, nomecdre"';
$form->addInput('Plano de Contas', 'plaid', 'text', "value='{$plaid}' pattern='^\d+\s*-\s*.+$' placeholder='Buscar' required", 'col-6 search', $dataFilters);

//Informações complementares para itens cadastrados
if($sql_rowid)
{
    require_once('app/modules/add_info.php');
    $addInfo = addInfo($connect, $codit, $usuca, $usuat, $dtcad, $dtatu);
    $form->addHtml($addInfo);
}

//Renderização da página
$output = "<div id='register_client' class='page'><header class='mainheader'><h2>Cadastro de Itens</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Item</span></a>" : '';
$output .= "</header>";
$output .= $form->renderForm();
$output .= "</div>";

return $output;