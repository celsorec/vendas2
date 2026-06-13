<?php

//Para acionar método update na process.php
$idUpdate = filter_input(INPUT_GET, 'idUpdate', FILTER_VALIDATE_INT);

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 5;                   //Permite novos cadastros
if(($idUpdate)) $aceusPageId = 6;   //Evita edição/exclusão não autorizadas
aceusAllowed($aceusPageId);

//Para acionar método update na process.php
$_SESSION['idUpdate']  = $idUpdate ?? '';
$_SESSION['tableName'] = 'forne';

//Buscando dados a partir do idUpdate
$columns = ['codfo', 'razfo', 'fanfo', 'docfo', 'fonfo', 'estfo', 'cidfo', 'logfo', 'cepfo', 'numfo', 'baifo', 'comfo', 'emafo', 'urlfo', 'obsfo', 'usuca', 'usuat', 'dtcad', 'dtatu', 'sql_rowid'];
if(!empty($idUpdate))
{
    $linkDelete = "process.php?idDelete=$idUpdate";
    $dataItems  = $connect->read($columns, 'forne', "WHERE sql_rowid='$idUpdate' AND SQL_DELETED='F'");

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

//Formulário fornecedor
$form = new FormHelper('process.php');
$form->addInput('Razão Social', 'razfo', 'text', "value='{$razfo}' placeholder='Nome completo (pessoas físicas)' minlength='5' required", 'col-3');
$form->addInput('Nome Fantasia', 'fanfo', 'text', "value='{$fanfo}' placeholder='Apelido (pessoas físicas)' minlength='2' required", 'col-3');
$form->addInput('CNPJ / CPF', 'docfo', 'text', "value='{$docfo}' placeholder='Digite apenas os números' minlength='14' required", 'col-3');
$form->addInput('Telefone com DDD', 'fonfo', 'text', "value='{$fonfo}' placeholder='Digite apenas os números' maxlength='15' minlength='15' required", 'col-3');

$form->addInput('CEP', 'cepfo', 'text', "value='{$cepfo}' minlength='8' maxlength='8' inputmode='numeric' placeholder='Digite apenas números' required", 'col-3');
$form->addInput('Estado', 'estfo', 'text', "value='{$estfo}' maxlength='2' required", 'col-3');
$form->addInput('Cidade', 'cidfo', 'text', "value='{$cidfo}' minlength='5' required", 'col-3');
$form->addInput('Bairro', 'baifo', 'text', "value='{$baifo}' minlength='5' required", 'col-3');
$form->addInput('Logradouro', 'logfo', 'text', "value='{$logfo}' placeholder='Rua, Avenida, Praça...' minlength='5' required", 'col-3');
$form->addInput('Número', 'numfo', 'text', "value='{$numfo}'", 'col-3');
$form->addInput('Complemento', 'comfo', 'text', "value='{$comfo}'", 'col-3');
$form->addInput('Email', 'emafo', 'email', "value='{$emafo}' placeholder='email@fornecedor.com.br' required", 'col-3');
$form->addInput('Site', 'urlfo', 'text', "value='{$urlfo}' placeholder='fornecedor.com.br' pattern='^(?:www\.)?[a-z0-9\-]+\.[a-z]{2,}(?:\.[a-z]{2,})?$' required", 'col-3');
$form->addInput('Observação', 'obsfo', 'text', "value='{$obsfo}'", 'col-3');

//Informações complementares para itens cadastrados
if($sql_rowid)
{
    require_once('app/modules/add_info.php');
    $addInfo = addInfo($connect, $codfo, $usuca, $usuat, $dtcad, $dtatu);
    $form->addHtml($addInfo);
}

//Renderização da página
$output = "<div id='register_client' class='page'><header class='mainheader'><h2>Cadastro de Fornecedores</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Fornecedor</span></a>" : '';
$output .= "</header>";
$output .= $form->renderForm();
$output .= "</div>";

return $output;