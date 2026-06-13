<?php
//Informações complementares para itens cadastrados
function addInfo($connect, $codig, $usuca, $usuat, $dtcad, $dtatu)
{
    $nomusUsuca = $connect->read(['nomus'], 'usuar', "WHERE codus='$usuca'");
    $nomusUsuat = $connect->read(['nomus'], 'usuar', "WHERE codus='$usuat'");

    $info  = '<div class="col-12" id="add-info">';
    $info .= '<h3>Informações complementares</h3>';
    $info .= '<p>';
    $info .= 'Código: <strong>'.$codig.'</strong>';
    $info .= ' | Cadastrado por <strong>'.$nomusUsuca[0]['nomus'].'</strong>';
    $info .= ' | Cadastrado em <strong>'.date('d/m/Y', strtotime($dtcad)).'</strong>';
    $info .= !empty($dtatu) ? ' | Última atualização: <strong>'.date('d/m/Y', strtotime($dtatu)).'</strong>':'';
    $info .= !empty($usuat) ? ' | Atualizado por <strong>'.$nomusUsuat[0]['nomus'].'</strong>':'';
    $info .= '</p><br>';
    $info .= '</div>';

    return $info;
}