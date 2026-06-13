<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 17;
aceusAllowed($aceusPageId);

//Parâmetros via URL
$pageName = isset($_GET['page']) ? htmlspecialchars($_GET['page'], ENT_QUOTES) : '';
$perio    = (isset($_GET['perio']) && !empty($_GET['perio'])) ? htmlspecialchars($_GET['perio'], ENT_QUOTES) : date('Y');
$filia    = (isset($_GET['filia']) && !empty($_GET['filia'])) ? htmlspecialchars($_GET['filia'], ENT_QUOTES) : '';

//Filtros da query SQL
$filterSQL  = !empty($perio) ? "perio={$perio} AND" : '';
$filterSQL .= !empty($filia) ? "filia={$filia} AND" : '';

//Paginação, antes da busca SQL
$pages = pagination($connect, 'saldo', $filterSQL); //return ['html' => $html, 'limit' => $limit, 'start' => $start];

//Buscando dados a partir do idUpdate
$columns = ['codsa', 'perio', 'saldo', 'filia', 'usuca', 'usuat', 'dtcad', 'dtatu', 'SQL_ROWID'];
$dataItems = $connect->read($columns, 'saldo', "WHERE $filterSQL SQL_DELETED='F' ORDER BY SQL_ROWID DESC LIMIT ".$pages['start'].", ".$pages['limit']);

if(!is_array($dataItems))
{
    MessageHelper::setMessage('Informação não localizada', 'alert');
    $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
    header("Location: index.php?page=".$linkPage);
    exit;
}

//Tratando informações para apresentação na tabela de dados
$filiaId = array_column($dataItems, 'filia');
$usucaId = array_column($dataItems, 'usuca');
$usuatId = array_column($dataItems, 'usuat');

//Obtendo nomes; filiais e usuários
$filiaName = $connect->read(['filialnome'], 'bancodados.bancosfil', "WHERE SQL_ROWID='{$filiaId[0]}'");
$usucaName = $connect->read(['nomus'], 'usuar', "WHERE codus='{$usucaId[0]}'");
$usuatName = $connect->read(['nomus'], 'usuar', "WHERE codus='{$usuatId[0]}'");

foreach($dataItems as $key => $value)
{
    foreach($value as $index => $item)
    {
        //Nomes; filiais e usuários
        if($index === 'filia') $dataItems[$key][$index] = $filiaName[0]['filialnome'];                                 //Filial
        if($index === 'usuca') $dataItems[$key][$index] = $usucaName[0]['nomus'];                                      //Quem cadastrou
        if($index === 'usuat') $dataItems[$key][$index] = isset($usuatName[0]['nomus']) ? $usuatName[0]['nomus'] : ''; //Quem atualizou

        //Datas em Português
        if($index === 'dtcad') $dataItems[$key][$index] = date('d/m/Y', strtotime($item));
        if($index === 'dtatu') $dataItems[$key][$index] = !is_null($item) ? date('d/m/Y', strtotime($item)) : '';

        //Link para formulário preenchido
        if($index === 'SQL_ROWID') $dataItems[$key][$index] = '<a class="btn-edit" href="?page=form_balance&idUpdate='.$item.'"></a>';
    }
}

//Header da tabela
$thead = [
    'codsa' => 'Código',
    'perio' => 'Período',
    'saldo' => 'Saldo',
    'filia' => 'Filial',
    'usuca' => 'Cadastrado por',
    'usuat' => 'Atualizado por',
    'dtcad' => 'Cadastrado em',
    'dtatu' => 'Atualizado em',
    'SQL_ROWID' => 'Ação'
];

//Formulário
$form = new FormHelper("index.php", "id='filters'", 'Filtros', 'Consultar', 'GET');
$form->addInput('page', 'page', 'text', "value='$pageName'", 'hidden');
$form->addInput('Ano', 'perio', 'text', "value='{$perio}' placeholder='0000' pattern='[0-9]{4}' required", 'col-3');

$filiaItems = FormHelper::getSelect(['sql_rowid', 'filialnome'], 'bancodados.bancosfil', "WHERE SQL_DELETED='F'"); //Pegando informações das filiais (centro de custos) de banco de dados externo
foreach($filiaItems as $key => $value) $filiaItems[$key] = trim(substr($value, 0, 49)); //Removendo 'S' e espaços em branco dos nomes das filiais; 'S' e espaços em branco são do banco de dados
$form->addSelect('Filial', 'filia', $filiaItems, '', "{$filia}", 'col-3');

$form->addInput('Resultados por Página', 'limit', 'number', "value='".$pages['limit']."' placeholder='10'", 'col-2');

//Datagrid
$dataGrid = new GridHelper();
$dataGrid->addHead($thead);
$dataGrid->addBody($dataItems);

//Renderização da página
$output  = "<div id='grid_balance' class='page'><header class='mainheader'><h2>Registros do Saldo Orçamentário</h2>";
$output .= "</header>";
$output .= $form->renderForm();
$output .= $dataGrid->renderGrid();
$output .= $pages['html'];
$output .= "</div>";

return $output;