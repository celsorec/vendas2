<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 8;
aceusAllowed($aceusPageId);

//Buscando dados a partir do idUpdate
$columns   = ['clapl', 'nompl', 'idpai', 'tippl', 'natpl', 'usuca', 'usuat', 'dtcad', 'dtatu', 'SQL_ROWID'];
$dataItems = $connect->read($columns, 'conta', "WHERE SQL_DELETED='F'");
if(!is_array($dataItems))
{
    MessageHelper::setMessage('Item não localizado', 'alert');
    $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
    header("Location: index.php?page=".$linkPage);
    exit;
}

//Ordenando esquema hierárquico
function treeCharts(array $rows)
{
    // Primeiro, indexa todos os elementos pelo SQL_ROWID
    $index = [];
    foreach($rows as $row)
    {
        $row['child'] = [];
        $index[$row['SQL_ROWID']] = $row;
    }

    $tree = []; //Árvore final
    foreach($index as $id => &$item)
    {
        if($item['idpai'] === null)
        {
            $tree[] = &$item; //Item raiz (sem pai)
        }
        else
        {
            //Adiciona como filho do respectivo pai
            if(isset($index[$item['idpai']]))
            {
                $index[$item['idpai']]['child'][] = &$item;
            }
            //Caso o idpai não exista, simplesmente ignora ou trata como raiz
        }
    }
    return $tree;
}
$tree = treeCharts($dataItems);

//Montando HTML
function getTree($tree)
{
    $html = "<ul>";
    foreach ($tree as $item)
    {
        $html .= "<li>";

        //Texto exibido
        $html .= '<a href="?page=form_chart&idUpdate='.$item['SQL_ROWID'].'">'.$item['nompl'].' - '.$item['clapl'].'</a>';

        //Se tiver filhos, renderiza recursivamente
        if(!empty($item['child']))
        {
            $html .= getTree($item['child']);
        }

        $html .= "</li>";
    }
    $html .= "</ul>";
    return $html;
}
$html = getTree($tree);

//Renderização da página
$output = "<div id='scheme_chart' class='page'><header class='mainheader'><h2>Hierarquia do Plano de Contas</h2>";
(isset($linkDelete) && !empty($linkDelete)) ? $output .= "<a href='{$linkDelete}' class='btn small-btn btn-delete'><span class='icon'></span><span class='text'>Excluir Plano</span></a>" : '';
$output .= "</header>";
$output .= $html;
$output .= "</div>";

return $output;