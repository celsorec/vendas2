<?php
//Obter listas no banco de dados
function getSelect(array $columns, string $table, string $filters='')
{
    $data = new DataRecord();
    $data = $data->read($columns, $table, $filters);
    $dataItems = [];
    foreach($data as $value)
    {
        $dataItems[$value[$columns[0]]] = $value[$columns[1]];
    }
    return $dataItems;
}