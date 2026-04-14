<?php

if(session_status() === PHP_SESSION_NONE) session_start();

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