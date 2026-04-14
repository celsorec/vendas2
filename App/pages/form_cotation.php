<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 9;
aceusAllowed($aceusPageId);

//Para acionar método update
$idUpdate = filter_input(INPUT_GET, 'idUpdate', FILTER_VALIDATE_INT);
$_SESSION['idUpdate']  = $idUpdate ?? '';
$_SESSION['tableName'] = 'proce';

//Buscando dados a partir do idUpdate
$columns = ['codpr', 'codfo', 'codit', 'qtdto', 'unmed', 'usped', 'usuca', 'usuat', 'dtcad', 'dtatu', 'sql_rowid'];
if(!empty($idUpdate))
{
    $linkDelete = "process.php?idDelete=$idUpdate";
    $dataItems  = $connect->read($columns, 'proce', "WHERE sql_rowid='$idUpdate' AND SQL_DELETED='F'");
    
    //Cotação dados do banco de dados
    $joinSQL  = 'LEFT JOIN forne ON cotac.codfo = forne.codfo ';

    $columns2 = ['codpr', "CONCAT(cotac.codfo, ' - ', forne.fanfo) AS codfo", 'preco', 'obsct', 'cotac.sql_rowid'];
    $cotation = $connect->read($columns2, 'cotac', "$joinSQL WHERE codpr='".$dataItems[0]['codpr']."' AND cotac.SQL_DELETED='F'");

    if(!is_array($dataItems))
    {
        MessageHelper::setMessage('Solicitação não localizada', 'alert');
        $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
        header("Location: index.php?page=".$linkPage);
        exit;
    }
}
else //Impedindo acesso do formulário em branco por se tratar de um formulário de continuidade; apenas edição
{
    MessageHelper::setMessage('Solicitação não localizada', 'alert');
    $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
    header("Location: index.php?page=form_request");
    exit;
}

//Verificando permissões de acesso para adicionar cotação; apenas Cotador e Moderador podem ter acesso
if($_SESSION['tipus'] !== 'C' && $_SESSION['tipus'] !== 'M')
{
    MessageHelper::setMessage('Acesso negado.', 'alert');
    $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
    header("Location: index.php?page=form_request");
    exit;
}

//INÍCIO -- OPERAÇÕES ESPECIAIS: Mesclando PHP com JS
//COTAÇÃO - Valores oriundos do banco de dados para preencher campos dinâmicos, criados via JS
if(isset($cotation) && is_array($cotation))
{
    //Permissão para Modificar / Excluir Cotações de Compras
    $aceusPageId = 10;  
    aceusAllowed($aceusPageId);

    foreach($cotation as $key => $value) $stringify[] = $value;
    $stringify = json_encode($stringify);

    print
    "<script>
        let cotationValues = $stringify;
        localStorage.setItem('cotationValues', JSON.stringify(cotationValues));
    </script>";
}
else
{
    //Limpando localStorage para evitar preenchimento do formulário fora do modo de edição
    print
    "<script>
        localStorage.removeItem('cotationValues');
    </script>";
}
//FIM -- OPERAÇÕES ESPECIAIS: Mesclando PHP com JS

//Dados para preencher para edição
foreach($columns as $var) $values[$var] = $dataItems[0][$var] ?? $_SESSION['dataForm'][$var] ?? null;
extract($values);

//Obtendo nome do item através do código para preenhcer campo (00 - nome item)
if($codit)
{
    $codit = $connect->read(['codit', 'nomit'], 'itens', "WHERE codit='$codit'");
    $codit = $codit[0]['codit'].' - '.$codit[0]['nomit'];
}

//Formulário solicitação de compras
$form = new FormHelper('process.php', 'id="form_request"');

$dataFilters = '
data-class="search_field"
data-table="itens"
data-likes="codit, nomit"
data-columns="codit, nomit, sql_rowid"';
$form->addInput('Processo', 'codpr', 'hidden', "value='{$codpr}' required", 'hidden');
$form->addInput('Item', 'codit', 'text', "value='{$codit}' placeholder='Buscar' pattern='^\d+\s*-\s*.+$' required", 'col-3 search', $dataFilters);
$form->addInput('Quantidade Total', 'qtdto', 'text', "value='{$qtdto}' placeholder='Quantidade necessária' required", 'col-3');

//Unidade de medidas; transformar em tabela do banco dados / JSON?
$unmedItems = 
[
    'UN' => 'Unidade',
    'KG' => 'Quilos',
    'LT' => 'Litros',
    'CX' => 'Caixas',
    'PC' => 'Pacotes'
];
$form->addSelect('Unidade de Medida', 'unmed', $unmedItems, 'required', "{$unmed}", 'col-3');

$form->addHtml('<h3 class="col-12">Cotação</h3>');
$form->addHtml('<div class="columns dynamic-column col-8" id="request-quote"></div>');

//Informações dos envolvidos no processo de compra
/* if($usuca || $usped)
{
    $usucaName = isset($usuca) && !empty($usuca) ? $connect->read(['nomus'], 'usuar', "WHERE codus='$usuca'") : [0 => ['nomus' => 'Pendente']]; //Requerente
    $uspedName = isset($usped) && !empty($usped) ? $connect->read(['nomus'], 'usuar', "WHERE codus='$usped'") : [0 => ['nomus' => 'Pendente']]; //Moderador
    $reqInfo  = '<div class="col-12" id="users-info"><h3>Andamento da Solicitação</h3><p>';
    $reqInfo .= 'Requerente: <strong>'.$usuca.' - '.$usucaName[0]['nomus'].'</strong> | ';
    $reqInfo .= 'Moderador:  <strong>'.$usped.' - '.$uspedName[0]['nomus'].'</strong><p>';
    $reqInfo .= '<p><a class="btn small-btn" href="?page=form_order&idUpdate='.$idUpdate.'" >Moderar Solicitação</a></p>';
    $reqInfo .= '</p></div>';
    $form->addHtml($reqInfo);
} */

//Informações complementares para itens cadastrados
if($usuca)
{
    require_once('app/modules/add_info.php');
    $addInfo = addInfo($connect, $codpr, $usuca, $usuat, $dtcad, $dtatu);
    $form->addHtml($addInfo);
}

//Renderização da página
$output = "<div id='form_request' class='page'><header class='mainheader'><h2>Cotação da Compra</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Solicitação</span></a>" : '';
$output .= "</header>";
$output .= $form->renderForm();
$output .= "</div>";

return $output;