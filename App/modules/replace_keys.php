<?php

function replaceKeys(array $data, string $tableName): array
{
    //Busca arquivo JSON com as novas chaves para substituição
    $table = 'app/glossary/'.$tableName.'.json';

    if(file_exists($table))
    {
        $table = file_get_contents($table);
        $table = json_decode($table, true);
    }
    else
    {
        //Se não houver JSON para a tabela buscada, retornar nomes originais das colunas no banco de dados
        return $data;
        exit;
    }

    $update = [];
    foreach($data as $key => $value)
    {
        foreach($table as $i => $line) //Colunas do banco de dados
        {
            //Substituição de nomes das colunas por chaves amigáveis
            if($i == $key) $update[$line] = $value;
        }
    }
    return $update;
}