<?php

require_once 'config.php';
require_once 'Core/Message/MessageHelper.php';
require_once 'Core/Database/DataRecord.php';
require_once 'Core/Docs/ValidateDocs.php';
require_once 'Core/Logger/LoggerDatabase.php';
require_once 'Core/Files/FileHandler.php';

//Informações adicionais para o Banco de dados
require_once 'controllers/setInfo.php';

//Impedindo acesso de usuários não logados
if(!isset($_SESSION['mfran']) || $_SESSION['mfran'] == null)
{
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}

$connect = new DataRecord;
$data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

//Convertendo para nulos os campos em branco
if(is_array($data))
{
    foreach($data as $key => $value)
    {
        if($value === '') $data[$key] = null;
    }
}

$tableName = $_SESSION['tableName'];
$idUpdate  = $_SESSION['idUpdate'];
$idDelete  = isset($_GET['idDelete']) ? (int) filter_input(INPUT_GET, 'idDelete', FILTER_VALIDATE_INT) : null;

//Soft delete
if(!empty($idDelete))
{
    //Informações deletadas
    $infoLogger['DELT']['MATX'] = ['time' => date('H:i:s'), 'user' => $_SESSION['codus'], 'tableName' => $tableName, 'idDelete' => $idDelete];
    $infoLogger['DELT']['INFO'] = $connect->read(['*'], $tableName, "WHERE SQL_ROWID='{$idDelete}'")[0]; //Informações removidas
    
    //Evitando colunas administrativas no Log
    unset($infoLogger['DELT']['INFO']['SQL_DELETED']);
    unset($infoLogger['DELT']['INFO']['SQL_ROWID']);

    $results = $connect->delete($tableName, $idDelete);
    if($results === true)
    {
        LoggerDatabase::register('Logs/'.date('Y').'/'.date('m'), date('d_m_Y'), $infoLogger);
        MessageHelper::setMessage('Exclusão efetuada com sucesso', 'success');
    }
    else
    {
        MessageHelper::setMessage('ERRO: '.$_SESSION['message'], 'alert');
    }
    unset($_SESSION['dataForm']);
    unset($_SESSION['idUpdate']);
    unset($_SESSION['tableName']);
    $referer = explode('&', $_SERVER['HTTP_REFERER']); //Remover ID da URL
    header("Location: " .$referer[0]);
    exit;
}

//Configurações para diferentes tabelas do banco de dados
if(isset($data) && !empty($data))
{
    require_once 'controllers/'.$tableName.'.php';
}
else
{
    MessageHelper::setMessage('Dados inválidos. Tente novamente', 'alert');
    header("Location: " .$_SERVER['HTTP_REFERER']);
    exit;
}

//Atualizar no banco de dados; funções (save e update) chamadas em 'controllers/'.$tableName.'.php';
function update($data, string $tableSecundary='', $idSecundary='')
{
    //Variáveis externas
    global $idUpdate;
    global $tableName;
    global $connect;

    //Redefinindo nome da tabela de destino do banco dados e ID do Item a ser atualizado,
    //Se há uma tabela secundária (quando formulário envia dados para duas tabelas), use-a
    $tableName = !empty($tableSecundary) ? $tableSecundary : $tableName;
    $idUpdate  = !empty($idSecundary)    ? $idSecundary    : $idUpdate;

    $columns = array_keys($data);
    $infoLogger['UPDT']['MATX'] = ['time' => date('H:i:s'), 'tableName' => $tableName, 'idUpdate' => $idUpdate, 'user' => $_SESSION['codus']]; //Usuário, tabela e SQL_ROWID
    $infoLogger['UPDT']['BEFR'] = $connect->read($columns, $tableName, "WHERE SQL_ROWID='{$idUpdate}'")[0]; //Informações antes da alteração
    $infoLogger['UPDT']['AFTR'] = $data; //Novas informações, alteração

    $results = $connect->update($tableName, $data, $idUpdate);
    if($results === true)
    {
        //Registrando Logs; Mensagem de retorno
        LoggerDatabase::register('Logs/'.date('Y').'/'.date('m'), date('d_m_Y'), $infoLogger);
        MessageHelper::setMessage('Informações atualizadas com sucesso', 'success');
        unset($_SESSION['dataForm']);
    }
    else
    {
        MessageHelper::setMessage('ERRO: '.$_SESSION['message'], 'alert');
    }
}

function save($data, string $tableSecundary='')
{
    //Variáveis externas
    global $tableName;
    global $connect;

    //Redefinindo nome da tabela de destino do banco dados
    //Se há uma tabela secundária (quando formulário envia dados para duas tabelas), use-a
    $tableName = !empty($tableSecundary) ? $tableSecundary : $tableName;

    //Novas informações, gravação
    $infoLogger['SAVE']['MATX'] = ['time' => date('H:i:s'), 'tableName' => $tableName, 'user' => $_SESSION['codus']];
    $infoLogger['SAVE']['INFO'] = $data;

    //Salvar no banco de dados
    $results = $connect->save($tableName, $data);
    if($results === true)
    {
        //Registrando Logs e mensagem de retorno
        LoggerDatabase::register('Logs/'.date('Y').'/'.date('m'), date('d_m_Y'), $infoLogger);
        MessageHelper::setMessage('Informações salvas com sucesso', 'success');
        unset($_SESSION['dataForm']);
    }
    else
    {
        MessageHelper::setMessage('ERRO: '.$_SESSION['message'], 'alert');
    }
}