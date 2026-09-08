<?php

//Gerenciamento de sessões
if(session_status() === PHP_SESSION_NONE)
{
    $sessionPath = __DIR__.'/../../sessions';

    if(!is_dir($sessionPath))
    {
        mkdir($sessionPath, 0777, true);

        //Leitura e Escrita no Windows
        if(PHP_OS_FAMILY === 'Windows') exec('icacls "' . $sessionPath . '" /grant IIS_IUSRS:(OI)(CI)M');
    }

    session_save_path($sessionPath);
    session_start();
}

class MessageHelper
{
    public static function setMessage($message, $class): void
    {
        $_SESSION['message'] = $message;
        $_SESSION['classMessage'] = $class;
    }

    public static function getMessage(): string
    {
        $html  = "<div class='".$_SESSION['classMessage']." message'>";
        $html .= "<span class='icon-".$_SESSION['classMessage']."'>".$_SESSION['message']."</span>";
        $html .= "<button class='close'>x</button>";
        $html .= "</div>";

        unset($_SESSION['message']);
        unset($_SESSION['classMessage']);

        return $html;
    }
}