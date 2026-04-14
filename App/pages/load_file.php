<?php
//Acessar aquivos PDF através do sistema

$fileLink = $_GET['fileLink']; //URL do arquivo

if(isset($fileLink) && !empty($fileLink))
{
    if(file_exists('Files/'.$fileLink))
    {
        header('Location: Files/'.$fileLink);
        exit;
    };
}

MessageHelper::setMessage('Arquivo não localizado.', 'alert');
$linkPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);
header("Location: index.php?page=404");
exit;