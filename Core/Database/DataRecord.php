<?php

require_once 'Connect.php';
require_once __DIR__.'/../Message/MessageHelper.php';

class DataRecord
{
    private $pdo;

    //Sistema trabalha com múltiplos bancos de dados
    public function __construct(string $dbname='compras')
    {
        $this->pdo = new Connect($dbname);
    }

    //Leitura
    public function read(array $columns, string $table, string $filter='')
    {
        $query = "SELECT " .implode(',', $columns). " FROM {$table} {$filter}";
        $sql = $this->pdo->getConnection()->prepare($query);

        try
        {
            $sql->execute();
            if($sql->rowCount() > 0)
            {
                $data = $sql->fetchAll(PDO::FETCH_ASSOC);
                return $data;
            }
        }
        catch(PDOException $e)
        {
            MessageHelper::setMessage($e->getMessage(), 'alert');
            return false;
        }
    }

    //Salvar
    public function save($table, array $data): bool
    {
        $keys = array_keys($data);

        $columns = implode(', ', $keys);
        $links = ':'.implode(', :', $keys);

        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$links})";
        $sql = $this->pdo->getConnection()->prepare($query);

        $dataRecord = [];
        foreach($data as $columns => $value)
        {
            $dataRecord[':'.$columns] = $value;
        }

        try
        {
            $sql->execute($dataRecord);
            return true;
        }
        catch(PDOException $e)
        {
            MessageHelper::setMessage($e->getMessage(), 'alert');
            return false;
        }
    }

    //Atualizar
    public function update($table, array $data, $id): bool
    {
        $dataQuery = '';
        foreach($data as $column => $value)
        {
            $dataQuery .= "{$column}=:{$column}, ";
        }

        $dataQuery = substr($dataQuery, 0, -2);

        $query = "UPDATE {$table} SET {$dataQuery} WHERE SQL_ROWID={$id}";
        $sql = $this->pdo->getConnection()->prepare($query);

        $dataRecord = [];
        foreach($data as $column => $value)
        {
            $dataRecord[':'.$column] = $value;
        }

        try
        {
            $sql->execute($dataRecord);
            return true;
        }
        catch(PDOException $e)
        {
            MessageHelper::setMessage($e->getMessage(), 'alert');
            return false;
        }
    }

    //Deleção lógica
    public function delete($table, $idDelete): bool
    {
        $query = "UPDATE {$table} SET SQL_DELETED=:SQL_DELETED WHERE SQL_ROWID={$idDelete}";

        $sql = $this->pdo->getConnection()->prepare($query);
        $sql->bindValue('SQL_DELETED', 'T');

        try
        {
            $sql->execute();
            return true;
        }
        catch(PDOException $e)
        {
            MessageHelper::setMessage($e->getMessage(), 'alert');
            return false;
        }
    }

    //Último ID inserido
    public function getLastInsertId(): string
    {
        return $this->pdo->getConnection()->lastInsertId();
    }

    //Para fazer operações concretas no banco de dados (Transactions, Rollback, Commit)
    public function getConnection()
    {
        return $this->pdo->getConnection();
    }
}