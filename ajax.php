<?php
require_once 'Core/Forms/FormHelper.php';
require_once 'Core/Database/DataRecord.php';

if(session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

$dbname  = filter_input(INPUT_GET, 'dbname', FILTER_SANITIZE_SPECIAL_CHARS) ?? false;
$table   = filter_input(INPUT_GET, 'table', FILTER_SANITIZE_SPECIAL_CHARS);
$likes   = filter_input(INPUT_GET, 'likes', FILTER_SANITIZE_SPECIAL_CHARS);
$columns = filter_input(INPUT_GET, 'columns', FILTER_SANITIZE_SPECIAL_CHARS);
$search  = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
$limit   = filter_input(INPUT_GET, 'limit', FILTER_UNSAFE_RAW) ?? '5';
$filter  = filter_input(INPUT_GET, 'filter', FILTER_UNSAFE_RAW) ?? '';

//Conexão com banco dados, de acordo com parâmetro dbname
$connect = empty($dbname)
    ? new DataRecord()
    : new DataRecord($dbname);

//Limpando retorno quando campo estiver vazio
if(strlen($search) == 0)
{
    $json = [];
    print json_encode($json);
    return;
}

//Definindo string SQL de $filter
if(!empty($filter))
{
    $filter = explode(',', $filter);
    $filter = 'AND '.$filter[0].'=\''.$filter[1].'\'';
}

//Convertendo para Array
$columns = explode(',', $columns);
$likes   = explode(',', $likes);

//Monta LIKEs para cada coluna
$likeParts = array_map(function($col) use ($search) {
    return "$col LIKE '%$search%'";
}, $likes);

$query = "WHERE SQL_DELETED='F' AND (" . implode(' OR ', $likeParts) . ") $filter LIMIT $limit";
$ajax = $connect->read($columns, $table, $query);

//Montando JSON
if(is_array($ajax))
{
    $json = [];
    foreach($ajax as $key => $value)
    {
        foreach($value as $index => $item)
        {
            if($item != null)
            {
                $json[$key][$index] = $item;
            }
            else
            {
                $json[$key][$index] = ''; //Campos nulos
            }
        }
    }
    print json_encode($json);
}
else
{
    print json_encode([['error' => 'Não foi possível localizar. Tente novamente']]);
}