<?php

//Para acionar método update na process.php
$idUpdate = filter_input(INPUT_GET, 'idUpdate', FILTER_VALIDATE_INT);

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 7;                   //Permite novos cadastros
if(($idUpdate)) $aceusPageId = 8;   //Evita edição/exclusão não autorizadas
aceusAllowed($aceusPageId);

$_SESSION['idUpdate']  = $idUpdate ?? '';
$_SESSION['tableName'] = 'proce';

//Buscando dados a partir do idUpdate
$columns = ['codpr', 'codit', 'qtdto', 'unmed', 'usped', 'usuca', 'usuat', 'dtcad', 'dtatu', 'sql_rowid'];
if(!empty($idUpdate))
{
    $linkDelete = "process.php?idDelete=$idUpdate";
    $dataItems  = $connect->read($columns, 'proce', "WHERE sql_rowid='$idUpdate' AND SQL_DELETED='F'");

    if(!is_array($dataItems))
    {
        MessageHelper::setMessage('Solicitação não localizada', 'alert');
        $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
        header("Location: index.php?page=".$linkPage);
        exit;
    }
}

//Verificando permissões de acesso para modificar solicitação; apenas requerente e Cotador podem ter acesso
if(isset($dataItems[0]['usuca']) && !empty($dataItems[0]['usuca']))
{
    if($dataItems[0]['usuca'] !== $_SESSION['codus'] && $_SESSION['tipus'] !== 'C')
    {
        MessageHelper::setMessage('Acesso negado.', 'alert');
        $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
        header("Location: index.php?page=form_request");
        exit;
    }
}

//Dados para preencher formulário para edição
foreach($columns as $var) $values[$var] = $dataItems[0][$var] ?? $_SESSION['dataForm'][$var] ?? null;
extract($values);

//Obtendo nome do item através do código para preenhcer campo (00 - nome item)
$tmprc = '';
if($codit)
{
    $codit = $connect->read(['sql_rowid', 'nomit', 'preit'], 'itens', "WHERE sql_rowid='$codit'");
    $tmprc = $codit[0]['preit']; //Preço do item para adicionar ao formulário
    $codit = $codit[0]['sql_rowid'].' - '.$codit[0]['nomit'];
}

//Formulário solicitação de compras
$form = new FormHelper('process.php', 'id="form_request"');

$dataFilters = '
data-class="search_field_saldo"
data-table="itens"
data-likes="codit, nomit"
data-columns="codit, nomit, preit, sql_rowid"';
$form->addInput('Item', 'codit', 'text', "value='{$codit}' placeholder='Buscar' pattern='^\d+\s*-\s*.+$' required", 'col-4 search', $dataFilters);
$form->addInput('Preço de Referência', 'tmprc', 'number', "value='".(isset($tmprc) ? $tmprc : '')."' readonly required", 'col-2');
$form->addInput('Quantidade', 'qtdto', 'number', "value='{$qtdto}' placeholder='10,5' required", 'col-2');

//Unidade de medidas; transformar em tabela do banco dados / JSON?
$unmedItems = 
[
    'UN' => 'Unidade',
    'KG' => 'Quilos',
    'LT' => 'Litros',
    'CX' => 'Caixas',
    'PC' => 'Pacotes',
    'SC' => 'Serviço'
];
$form->addSelect('Unidade de Medida', 'unmed', $unmedItems, 'required', "{$unmed}", 'col-2');

//Obtendo saldo orçamentário

// ATENÇÃO: SALDO ORÇAMENTÁRIO PERTENCE À CONTA ORÇAMENTÁRIA NO PLANO DE CONTAS
// HÉLIO FICOU DE REESCREVER ESSA TABELA (DRE?)

/*$saldo = $connect->read(['saldo'], 'saldo', "WHERE perio='".date('Y')."' AND filia='".$_SESSION['filus']."'");
$preco = $connect->read(['SUM(preco) AS preco'], 'proce', "WHERE statu != 'CAN' AND filia='".$_SESSION['filus']."'");

$html  = '<div class="col-12">';
$html .= '<h3>Saldo Orçamentário '.date('Y').'</h3><p>';
$html .= 'Inicial:         R$ <strong id="inici">'.number_format($saldo[0]['saldo'], 2).'</strong> | ';
$html .= 'Consumido:       R$ <strong id="consu">'.number_format($preco[0]['preco'], 2).'</strong> | ';
$html .= 'Disponível:      R$ <strong id="dispo">'.number_format($saldo[0]['saldo'] - $preco[0]['preco'], 2).'</strong> | ';
$html .= 'Total da Compra: R$ <strong id="ttcpr">0</strong>';
$html .= '</p></div>';*/

//Campo oculto criado para comparar valores na process_settings.php; não é gravado no banco de dadpos
/*$form->addInput('S. Dispon.', 'sddis', 'number', "value='".number_format($saldo[0]['saldo'] - $preco[0]['preco'], 2)."'required", 'hidden');
$form->addHtml($html);*/

//Informações dos envolvidos no processo de compra
/*
if($usuca || $uscot || $usped)
{
    $usucaName = isset($usuca) && !empty($usuca) ? $connect->read(['nomus'], 'usuar', "WHERE codus='$usuca'") : [0 => ['nomus' => 'Pendente']]; //Requerente
    $uscotName = isset($uscot) && !empty($uscot) ? $connect->read(['nomus'], 'usuar', "WHERE codus='$uscot'") : [0 => ['nomus' => 'Pendente']]; //Cotador
    $uspedName = isset($usped) && !empty($usped) ? $connect->read(['nomus'], 'usuar', "WHERE codus='$usped'") : [0 => ['nomus' => 'Pendente']]; //Moderador
    $reqInfo  = '<div class="col-12" id="users-info"><h3>Andamento da Solicitação</h3><p>';
    $reqInfo .= 'Requerente: <strong>'.$usuca.' - '.$usucaName[0]['nomus'].'</strong> | ';
    $reqInfo .= 'Cotador:    <strong>'.$uscot.' - '.$uscotName[0]['nomus'].'</strong> | ';
    $reqInfo .= 'Moderador:  <strong>'.$usped.' - '.$uspedName[0]['nomus'].'</strong></p>';
    $reqInfo .= '<p><a class="btn small-btn" href="?page=form_cotation&idUpdate='.$idUpdate.'" >Adicionar Cotação</a></p>';
    $reqInfo .= '</div>';
    $form->addHtml($reqInfo);
}
*/

//Informações complementares para itens cadastrados
if($usuca)
{
    require_once('app/modules/add_info.php');
    $addInfo = addInfo($connect, $codpr, $usuca, $usuat, $dtcad, $dtatu);
    $form->addHtml($addInfo);
}

//Renderização da página
$output = "<div id='form_request' class='page'><header class='mainheader'><h2>Solicitação de Compra</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Solicitação</span></a>" : '';
$output .= "</header>";
$output .= $form->renderForm();
$output .= "</div>";

return $output;