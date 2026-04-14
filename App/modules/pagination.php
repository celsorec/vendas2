<?php
//Paginação para Datagrids
function pagination($connect, $tableName, $filterSQL): array
{
    //Obtendo informação para montar paginação de resultados
    $pnumb =  isset($_GET['pnumb']) ? filter_input(INPUT_GET, 'pnumb', FILTER_VALIDATE_INT) : 1;
    $limit = (isset($_GET['limit']) && $_GET['limit'] > 0) ? (int) $_GET['limit'] : 10;
    $start = ($pnumb * $limit) - $limit;

    //Obtendo última página; contagem
    $lpage = $connect->read(['count('.$tableName.'.SQL_ROWID) AS lpage'], $tableName, "$filterSQL $tableName.SQL_DELETED='F'"); //Última página
    $lpage = (int)$lpage[0]['lpage'];
    $lpage = ceil($lpage / $limit);

    //Obtendo URL para criar links da paginação
    $urlpr = $_GET;
    unset($urlpr['pnumb']);
    $urlpr = http_build_query($urlpr);

    //Paginação
    $html = '';
    if($lpage > 0)
    {
        $html .= '<div class="pagination module col-12">';
            if($pnumb > 1)
            {
                $html .= '<a class="small-btn" href="?'.$urlpr.'&pnumb=1" title="Página 1">Início</a>';
                $html .= '<a class="small-btn" href="?'.$urlpr.'&pnumb='.($pnumb - 1).'" title="Página '.($pnumb - 1).'">&lt;</a>';
            }
        
            $html .= '<span>Página '.$pnumb.' de '.$lpage.'</span>';
        
            if($pnumb < $lpage)
            {
                $html .= '<a class="small-btn" href="?'.$urlpr.'&pnumb='.($pnumb + 1).'" title="Página '.($pnumb + 1).'">&gt;</a>';
                $html .= '<a class="small-btn" href="?'.$urlpr.'&pnumb='.$lpage.'" title="Página '.$lpage.'">Fim</a>';
            }
        $html .= '</div>';
        return ['html' => $html, 'limit' => $limit, 'start' => $start];
    }
    else
    {
        MessageHelper::setMessage('Nenhum resultado, conforme filtragem', 'alert');
        $linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit;
    }
}