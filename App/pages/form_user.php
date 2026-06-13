<?php

//Para acionar método update na process.php
$idUpdate = filter_input(INPUT_GET, 'idUpdate', FILTER_VALIDATE_INT);

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 1;                   //Permite novos cadastros
if(($idUpdate)) $aceusPageId = 2;   //Evita edição/exclusão não autorizadas
aceusAllowed($aceusPageId);

//Para acionar método update na process.php
$_SESSION['idUpdate']  = $idUpdate ?? '';
$_SESSION['tableName'] = 'usuar';

//Ativar obrigatoriedade da senha para novos cadastros (senus)
$required = 'required';

//Buscando dados a partir do idUpdate
$columns = ['codus', 'nomus', 'senus', 'tipus', 'fonus', 'cpfus', 'aceus', 'filus', 'datus', 'emaus', 'atius', 'genus', 'usuca', 'usuat', 'dtcad', 'dtatu', 'sql_rowid'];
if(!empty($idUpdate))
{
    $linkDelete = "process.php?idDelete=$idUpdate";
    $dataItems  = $connect->read($columns, 'usuar', "WHERE sql_rowid='$idUpdate' AND SQL_DELETED='F'");

    if(!is_array($dataItems))
    {
        MessageHelper::setMessage('Usuário não localizado', 'alert');
        $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
        header("Location: index.php?page=".$linkPage);
        exit;
    }
    else
    {
        $required = null;
    }
}

//Dados para preencher para edição
foreach($columns as $var) $values[$var] = $dataItems[0][$var] ?? $_SESSION['dataForm'][$var] ?? null;
extract($values);

//Dados pessoais
$form = new FormHelper('process.php');
$form->addInput('Nome Completo', 'nomus', 'text', "value='{$nomus}' placeholder='Nome Completo' minlength='7' required", 'col-3');

//Senha só pode ser inserida para novos usuários
$form->addInput('Senha', 'senus', 'password', "placeholder='Use uma senha segura' minlength='8' {$required}", 'col-3');
$form->addInput('CPF', 'cpfus', 'text', "value='{$cpfus}' placeholder='Digite apenas os números' maxlength='14' minlength='14' required", 'col-3');
$form->addInput('Data de Nascimento', 'datus', 'date', "value='{$datus}' required", 'col-3');
$form->addInput('Telefone com DDD', 'fonus', 'text', "value='{$fonus}' placeholder='Digite apenas os números' maxlength='15' minlength='15' required", 'col-3');
$form->addInput('E-mail', 'emaus', 'email', "value='{$emaus}' placeholder='nome@email.com'", 'col-3');

//Ativo
$atiusItems = ['T' => 'Sim', 'F' => 'Não'];
$form->addSelect('Ativo', 'atius', $atiusItems, 'required', "{$atius}", 'col-3');

//Gênero
$genusItems = ['M' => 'Masculino', 'F' => 'Feminino', 'N' => 'Não Informado'];
$form->addSelect('Gênero', 'genus', $genusItems, 'required', "{$genus}", 'col-3');

//Tipo
$tipusItems = ['R' => 'Requerente', 'C' => 'Cotador', 'M' => 'Moderador'];
$form->addSelect('Tipo', 'tipus', $tipusItems, 'required', "{$tipus}", 'col-3');

$filusItems = FormHelper::getSelect(['sql_rowid', 'filialnome'], 'bancodados.bancosfil', "WHERE SQL_DELETED='F'"); //Pegando informações das filiais (centro de custos) de banco de dados externo
foreach($filusItems as $key => $value) $filusItems[$key] = trim(substr($value, 0, 49)); //Removendo 'S' e espaços em branco dos nomes das filiais; 'S' e espaços em branco são do banco de dados
$form->addSelect('Filial', 'filus', $filusItems, 'required', "{$filus}", 'col-3');

//Acesso a funções
$form->addHtml('<h3 class="col-12">Atribuições</h3>');
$form->addCheckbox($aceusItems, $aceus ?? '', 'col-7', 'check-items');

//Informações complementares para itens cadastrados
if($sql_rowid)
{
    require_once('app/modules/add_info.php');
    $addInfo = addInfo($connect, $codus, $usuca, $usuat, $dtcad, $dtatu);
    $form->addHtml($addInfo);
}

//Renderização da página
$output = "<div id='register_client' class='page'><header class='mainheader'><h2>Cadastro de Usuários</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Usuário</span></a>" : '';
$output .= "</header>";
$output .= $form->renderForm();
$output .= "</div>";

return $output;