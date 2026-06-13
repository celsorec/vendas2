<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 0;
aceusAllowed($aceusPageId);

//Recebendo aquivo com função replaceKeys
require 'app/modules/replace_keys.php';

//Recebendo parâmetros via GET para navegação
$nav['year']  = $_GET['year']  ?? '';
$nav['month'] = $_GET['month'] ?? '';
$nav['file']  = $_GET['file']  ?? '';
$logFiles = LoggerDatabase::read($nav['year'], $nav['month'], $nav['file']);

//Breadcrumbs
$urlget = '?page=read_logs';
$bread  = [];
foreach($nav as $key => $value)
{
    $bread['Início'] = '?page=read_logs';
    $urlget .= '&'.$key.'='.$value;
    if($value) $bread[$value] = $urlget;
}

$breadcrumbs = '<p class="breadcrumbs">';
foreach($bread as $text => $link) $breadcrumbs .= '<a href="'.$link.'">'.str_replace(['_', '.json'], ['/', ''], $text).'<span> > </span></a>';
$breadcrumbs .= '</p>';

//Apresentação
$html = $breadcrumbs;
if(is_array($logFiles))
{
    //Se são pastas, listar subpastas ou arquivos
    $html .= '<ul class="list-folders">';
    foreach($logFiles as $key => $value)
    {
        $html .= '<li><a class="btn small-btn btn-folder" href="?page=read_logs'.$value.'"><span class="icon"></span><span class="text">'.str_replace(['_', '.json'], ['/', ''], $key).'</span></a></li>';
    }
    $html .= '</ul>';
}
else
{
    //Se é arquivo, apresentar
    $logFiles = json_decode($logFiles, true);
    foreach($logFiles as $key => $value)
    {
        //Save
        if(isset($value['SAVE']))
        {
            //Substituindo chaves por nomes amigáveis
            $value['SAVE']['INFO'] = replaceKeys($value['SAVE']['INFO'], $value['SAVE']['MATX']['tableName']);

            $save  = '<div class="logs save">';
            $save .= '<p>Às <strong>'.$value['SAVE']['MATX']['time'].'</strong> o usuário <strong>'.$value['SAVE']['MATX']['user'].'</strong> criou na tabela de dados <strong>'.$value['SAVE']['MATX']['tableName'].'</strong> o seguinte registro:<br>';
            foreach($value['SAVE']['INFO'] as $data => $info) $save .= $data.': '.$info.'; ';
            $save .= '</p></div>';
            $html .= $save;
        }

        //Delete
        if(isset($value['DELT']))
        {
            //Substituindo chaves por nomes amigáveis
            $value['DELT']['INFO'] = replaceKeys($value['DELT']['INFO'], $value['DELT']['MATX']['tableName']);

            $delt  = '<div class="logs delt">';
            $delt .= '<p>Às <strong>'.$value['DELT']['MATX']['time'].'</strong> o usuário <strong>'.$value['DELT']['MATX']['user'].'</strong> removeu da tabela de dados <strong>'.$value['DELT']['MATX']['tableName'].'</strong> o registro <strong>'.$value['DELT']['MATX']['idDelete'].'</strong></p>';
            $delt .= '<p>Informações removidas: <br>';
            foreach($value['DELT']['INFO'] as $data => $info) $delt .= $data.': '.$info.'; ';
            $delt .= '</p></div>';
            $html .= $delt;
        }

        //Update
        if(isset($value['UPDT']))
        {
            $updt = '<div class="logs updt">';
            foreach($value['UPDT'] as $data => $info)
            {
                //Informações principais
                if($data == 'MATX')
                {
                    $updt .= '<p> Às <strong>'.$info['time'].'</strong> o usuário <strong>'.$info['user'].'</strong> modificou o registro <strong>'.$info['idUpdate'].'</strong> da tabela <strong>'.$info['tableName'].'</strong></p>';
                }

                //Antes
                if($data == 'BEFR')
                {
                    //Substituindo chaves por nomes amigáveis
                    $before = replaceKeys($info, $value['UPDT']['MATX']['tableName']);

                    //Montando HTML
                    $updt .= '<p>Informação antes da mudança: <br>';
                    foreach($before as $i => $befr) $updt .= $i.': '.$befr.'; ';
                    $updt .= '</p>';
                }

                //Depois
                if($data == 'AFTR')
                {
                    //Substituindo chaves por nomes amigáveis
                    $after = replaceKeys($info, $value['UPDT']['MATX']['tableName']);

                    //Montando HTML
                    $updt .= '<p>Informação depois da mudança: <br>';                        
                    foreach($after as $i => $aftr)
                    {
                        //Compara alterações para destacar mudanças
                        if($aftr != $before[$i])
                        $updt .= '<strong>'.$i.': '.$aftr.'</strong>; ';
                        else
                        $updt .= $i.': '.$aftr.'; ';
                    }
                    $updt .= '</p>';
                }
            }
            $updt .= '</div>';
            $html .= $updt;
        }
    }
}

$output = <<<OUTPUT
<div id='read_logs' class='page'>
    <header>
        <h2>Logs do Sistema</h2>
    </header>
    
    <div class='columns'>
        <div class='fields-group search col-12'>
            {$html}
        </div>
    </div>
</div>
OUTPUT;

return $output;