<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 9;
aceusAllowed($aceusPageId);

//Parâmetros via URL
$pageName =  isset($_GET['page']) ? htmlspecialchars($_GET['page'], ENT_QUOTES) : '';
$dtini    = (isset($_GET['dtini']) && !empty($_GET['dtini'])) ? htmlspecialchars($_GET['dtini'], ENT_QUOTES) : '';
$dtfin    = (isset($_GET['dtfin']) && !empty($_GET['dtfin'])) ? htmlspecialchars($_GET['dtfin'], ENT_QUOTES) : '';
$codit    = (isset($_GET['codit']) && !empty($_GET['codit'])) ? htmlspecialchars($_GET['codit'], ENT_QUOTES) : '';

//Filtros da query SQL
$filterSQL  = !empty($dtini) ? "proce.dtcad >= '$dtini' AND " : '';
$filterSQL .= !empty($dtfin) ? "proce.dtcad <= '$dtfin' AND " : '';
$filterSQL .= !empty($codit) ? "proce.codit  = '".(explode(' - ', $codit)[0])."' AND " : '';

//Junção de tabelas usando LEFT JOIN para obter nomes de informações salvas por código
$joinSQL  = 'LEFT JOIN itens ON proce.codit = itens.codit ';    //Itens para compra
$joinSQL .= 'LEFT JOIN usuar ON proce.usuca = usuar.codus ';    //Requrente
$joinSQL .= 'LEFT JOIN cotac ON proce.codpr = cotac.codpr ';    //Para processos já cotados

//Paginação, antes da busca SQL
$pages = pagination($connect, 'proce', $joinSQL .' WHERE cotac.codpr IS NULL AND '. $filterSQL); //return ['html' => $html, 'limit' => $limit, 'start' => $start];

//Buscando dados a partir do idUpdate
$columns   = ['proce.codpr', 'itens.nomit AS codit', 'qtdto', 'proce.preco', 'usuar.nomus AS usuca', 'proce.dtcad', 'proce.SQL_ROWID'];
$dataItems = $connect->read($columns, 'proce', "$joinSQL WHERE $filterSQL cotac.codpr IS NULL AND proce.SQL_DELETED='F' ORDER BY proce.SQL_ROWID DESC LIMIT ".$pages['start'].", ".$pages['limit']);

if(!is_array($dataItems))
{
    MessageHelper::setMessage('Nenhuma solicitação localizada', 'alert');
    $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
    header("Location: index.php?page");
    exit;
}

//Ajustando dados para tabela de apresentação
foreach($dataItems as $key => $value)
{
    foreach($value as $index => $item)
    {
        //Data em Português
        if($index === 'dtcad') $dataItems[$key][$index] = date('d/m/y', strtotime($item));
        
        //Adicionando link para formulário
        if($index === 'SQL_ROWID')
        {
            $dataItems[$key][$index] = '<a title="Adicionar Cotação" class="forward-btn" href="?page=form_cotation&idUpdate='.$item.'"></a>';
        }
    }
}

//Header da tabela
$thead = [
    'codpr' => 'Processo',
    'nomit' => 'Item',
    'qtdto' => 'QTD.',
    'preco' => 'Preço Médio',
    'usuca' => 'Requerente',
    'dtcad' => 'Data Requisição',
    'SQL_ROWID' => 'Ações'
];

//Formulário de filtragem de resultados
$form = new FormHelper("index.php", "id='filters'", 'Filtros', 'Consultar', 'GET');
$form->addInput('page', 'page', 'text', "value='$pageName'", 'hidden');

$form->addInput('Data Inicial', 'dtini', 'date', "value='$dtini'", 'col-2');
$form->addInput('Data Final', 'dtfin', 'date', "value='$dtfin'", 'col-2');

$dataFilters = '
data-class="search_field"
data-table="itens"
data-likes="codit, nomit"
data-columns="codit, nomit, sql_rowid"';
$form->addInput('Item', 'codit', 'text', "value='{$codit}' placeholder='Buscar' pattern='^\d+\s*-\s*.+$'", 'col-3 search', $dataFilters);
$form->addInput('Resultados por Página', 'limit', 'number', "value='".$pages['limit']."' placeholder='10'", 'col-2');

//Datagrid
$dataGrid = new GridHelper();
$dataGrid->addHead($thead);
$dataGrid->addBody($dataItems);

//Renderização da página
$output  = "<div id='grid_balance' class='page'><header class='mainheader'><h2>Processos de Compras (Solicitações)</h2>";
$output .= "</header>";
$output .= $form->renderForm();
$output .= $dataGrid->renderGrid();
$output .= $pages['html'];
$output .= "</div>";

return $output;