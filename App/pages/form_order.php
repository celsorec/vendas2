<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 11;
aceusAllowed($aceusPageId);

//Para acionar método update
$idUpdate = filter_input(INPUT_GET, 'idUpdate', FILTER_VALIDATE_INT);
$_SESSION['idUpdate']  = $idUpdate ?? '';
$_SESSION['tableName'] = 'proce';

//Buscando dados a partir do idUpdate
$columns = ['codpr', 'codfo', 'codit', 'qtdto', 'unmed', 'preco', 'obsev', 'files', 'usped', 'usuca', 'usuat', 'dtcad', 'dtatu', 'sql_rowid'];
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

//Verificando permissões de acesso para converter cotação em pedido de compras; apenas Moderador pode ter acesso
if($_SESSION['tipus'] !== 'M')
{
    MessageHelper::setMessage('Acesso negado.', 'alert');
    $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
    header("Location: index.php?page=form_request");
    exit;
}

//INÍCIO -- OPERAÇÕES ESPECIAIS: Mesclando PHP com JS
//COTAÇÃO - Valores oriundos do banco de dados para preencher campos dinâmicos, criados via JS
if(isset($cotation) && !empty($cotation))
{
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
    $codit = $connect->read(['sql_rowid', 'nomit'], 'itens', "WHERE sql_rowid='$codit'");
    $codit = $codit[0]['sql_rowid'].' - '.$codit[0]['nomit'];
}

//Obtendo nome do fornecedor através do código para preenhcer campo (00 - nome item)
if($codfo)
{
    $codfo = $connect->read(['sql_rowid', 'fanfo'], 'forne', "WHERE sql_rowid='$codfo'");
    $codfo = $codfo[0]['sql_rowid'].' - '.$codfo[0]['fanfo'];
}

//Quando já há um fornecedor ($codfo); indica que já é um pedido de compra, não apenas cotação
if(isset($codfo) && !empty($codfo))
{
    $aceusPageId = 12;
    aceusAllowed($aceusPageId);
}

//Formulário solicitação de compras
$form = new FormHelper('process.php', 'id="form_request"', '', 'Salvar', 'POST', 'multipart/form-data');

$dataFilters = '
data-class="search_field"
data-table="itens"
data-likes="codit, nomit"
data-columns="codit, nomit, sql_rowid"';
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

$dataFilters = '
data-class="search_field"
data-table="forne"
data-likes="codfo, fanfo"
data-columns="codfo, fanfo, sql_rowid"';
$form->addInput('Fornecedor', 'codfo', 'text', "value='{$codfo}' placeholder='Buscar' pattern='^\d+\s*-\s*.+$' required", 'col-3 search', $dataFilters);
$form->addInput('Preço do Fornecedor', 'preco', 'number', "value='{$preco}' placeholder='0,00' step='0.01' required", 'col-3');
$form->addInput('Observação', 'obsev', 'text', "value='{$obsev}' placeholder='Motivo do cancelamento'", 'col-3');

//Acessar/atualizar arquivos PDF
$hiddenClass = ''; //Classe para ocultar/revelar campo para upload de novo PDF (substituição) e link para acesso ao PDF (access-change-pdf.js)
$accessPDF   = ''; //Botão para baixar arquivo PDF de contrato com fornecedor
$changePDF   = ''; //Botão para ocultar/revelar campo para upload de novo PDF (substituição) e link para acesso ao PDF (access-change-pdf.js)
if($files)
{
    $hiddenClass = 'hidden';
    $linkFile = '?page=load_file&fileLink='.$files;
    $accessPDF = '
    <div class="fields-group col-3" id="access-pdf">
        <div class="label">Contrato Fornecedor</div>

        <a class="btn small-btn" id="btn-access-pdf" href="'.$linkFile.'">
            <span class="icon"></span>
            <span class="text">Acessar PDF</span>
        </a>
    </div>';

    $changePDF = '
    <div class="fields-group col-3" id="change-pdf">
        <div class="label">Contrato Fornecedor</div>

        <div class="btn small-btn" id="btn-change-pdf">
            <span class="icon"></span>
            <span class="text">Substituir PDF</span>
        </div>
    </div>';
}
//Campo não existe na tabela; serve apenas para função de salvar arquivo; Caminho atual do arquivo 
$form->addInput('Caminho / Nome do Arquivo', 'cpath', 'text', "value='$files'", "hidden");
$form->addInput('Enviar Contrato PDF', 'files', 'file', "accept='.pdf'", "col-3 $hiddenClass");
$form->addHtml($accessPDF);
$form->addHtml($changePDF);

//Campo oculto codpr (Código do Processo) para compor nome do arquivo PDF
$form->addInput('codpr', 'codpr', 'text', "value='{$codpr}' readonly", 'hidden');

//Div dinâmica para cotação JS
$form->addHtml('<h3 class="col-12">Cotação</h3>');
$form->addHtml('<div class="columns dynamic-column col-8" id="request-order"></div>');

//Informações dos envolvidos no processo de compra
/* if($usreq || $uscot || $usped)
{
    $usreqName = isset($usreq) && !empty($usreq) ? $connect->read(['nomus'], 'usuar', "WHERE codus='$usreq'") : [0 => ['nomus' => 'Pendente']]; //Requerente
    $uscotName = isset($uscot) && !empty($uscot) ? $connect->read(['nomus'], 'usuar', "WHERE codus='$uscot'") : [0 => ['nomus' => 'Pendente']]; //Cotador
    $uspedName = isset($usped) && !empty($usped) ? $connect->read(['nomus'], 'usuar', "WHERE codus='$usped'") : [0 => ['nomus' => 'Pendente']]; //Moderador
    $reqInfo  = '<div class="col-12" id="users-info"><h3>Andamento da Solicitação</h3><p>';
    $reqInfo .= 'Requerente: <strong>'.$usreq.' - '.$usreqName[0]['nomus'].'</strong> | ';
    $reqInfo .= 'Cotador:    <strong>'.$uscot.' - '.$uscotName[0]['nomus'].'</strong> | ';
    $reqInfo .= 'Moderador:  <strong>'.$usped.' - '.$uspedName[0]['nomus'].'</strong>';
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
$output = "<div id='form_request' class='page'><header class='mainheader'><h2>Pedido de Compra</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Solicitação</span></a>" : '';
$output .= "</header>";
$output .= $form->renderForm();
$output .= "</div>";

return $output;