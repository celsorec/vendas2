<?php

//Para acionar método update na process.php
$idUpdate = filter_input(INPUT_GET, 'idUpdate', FILTER_VALIDATE_INT);

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 2;                   //Permite novos cadastros
if(($idUpdate)) $aceusPageId = 3;   //Evita edição/exclusão não autorizadas
aceusAllowed($aceusPageId);

//Para acionar método update na process.php
$_SESSION['idUpdate']  = $idUpdate ?? '';
$_SESSION['tableName'] = 'categ';

//Buscando dados a partir do idUpdate
$columns = ['codca', 'nomca', 'desca', 'idpai', 'usuca', 'usuat', 'dtcad', 'dtatu', 'sql_rowid'];
if(!empty($idUpdate))
{
    $linkDelete = "process.php?idDelete=$idUpdate";
    $dataItems  = $connect->read($columns, 'categ', "WHERE sql_rowid='$idUpdate' AND SQL_DELETED='F'");

    if(!is_array($dataItems))
    {
        MessageHelper::setMessage('Categoria não localizada', 'alert');
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
    $idpai = $connect->read(['sql_rowid', 'nomca'], 'categ', "WHERE sql_rowid='$idpai' AND SQL_DELETED='F'");
    $idpai = $idpai[0]['sql_rowid'].' - '.$idpai[0]['nomca'];
}

//Formulário categorias
$form = new FormHelper('process.php');
$form->addInput('Nome da Categoria', 'nomca', 'text', "value='{$nomca}' placeholder='Material de Escritório' minlength='5' required", 'col-3');

$dataFilters = '
data-class="categ_field"
data-table="categ"
data-likes="codca, nomca"
data-columns="codca, nomca, sql_rowid"';
$form->addInput('Categoria Superior', 'idpai', 'text', "value='{$idpai}' pattern='^\d+\s*-\s*.+$' placeholder='Buscar'", 'col-3 search', $dataFilters);

$form->addInput('Descrição da Categoria', 'desca', 'text', "value='{$desca}' placeholder='Descreva o objetivo e os tipos de itens dessa categoria' minlength='5'", 'col-6');

//Informações complementares para itens cadastrados
if($sql_rowid)
{
    require_once('app/modules/add_info.php');
    $addInfo = addInfo($connect, $codca, $usuca, $usuat, $dtcad, $dtatu);
    $form->addHtml($addInfo);
}

//Renderização da página
$output = "<div id='register_client' class='page'><header class='mainheader'><h2>Cadastro de Categorias de Itens</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Categoria</span></a>" : '';
$output .= "</header>";
$output .= $form->renderForm();

//Categorias cadastradas
function registredCategories(array $array)
{
    $html = "<ul class='categories'>";
    foreach($array as $items)
    {
        $html .= '<li><a href="?page=form_category&idUpdate='.$items['sql_rowid'].'" title="'.$items['desca'].'" class="items">'.$items['nomca'].'</a></li>';
    }
    $html .= "</ul>";
    return $html;
}
$categories = $connect->read(['sql_rowid', 'nomca', 'desca'], 'categ', "WHERE SQL_DELETED='F'");
$output .= '<h3 class="col-12">Categorias cadastradas</h3>';
$output .= registredCategories($categories);
$output .= "</div>";

return $output;