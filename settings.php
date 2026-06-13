<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('default_charset', 'UTF-8');
ini_set('max_execution_time', '0');
setlocale(LC_ALL, 'pt_BR');

//Erros
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errors/log_' . date('Y-m-d__H-i-s') . '.txt');
error_reporting(E_ALL);

function errorHandler($errno, $errstr, $errfile, $errline)
{
    error_log("Erro [$errno] em $errfile:$errline - $errstr");
    http_response_code(500);
    echo "Ocorreu um erro interno. Tente novamente mais tarde.";
    exit;
}

function exceptionHandler(Throwable $exception)
{
    $mensagem = sprintf(
        "Exceção não capturada: %s | Arquivo: %s | Linha: %d",
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );

    error_log($mensagem);
    error_log($exception->getTraceAsString());

    http_response_code(500);
    echo "Erro inesperado. Tente novamente."
    exit;
}

function shutdownHandler()
{
    $error = error_get_last();
    if($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR]))
    {
        error_log("Erro fatal: {$error['message']} em {$error['file']}:{$error['line']}");
        http_response_code(500);
        echo "Erro crítico. Tente novamente.";
        exit;
    }
}

set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
register_shutdown_function('shutdownHandler');

//Evitando múltiplos redirecionamentos
/* if(!isset($_SESSION['redirect_count'])) $_SESSION['redirect_count'] = 0;
$_SESSION['redirect_count']++;
if($_SESSION['redirect_count'] > 5) 
{
    header('Location: ?page=404');
    unset($_SESSION['redirect_count']);
    exit;
} */