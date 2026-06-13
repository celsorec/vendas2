<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('default_charset', 'UTF-8');
ini_set('max_execution_time', '0');
setlocale(LC_ALL, 'pt_BR');

ini_set('display_errors', 1);          //Exibição de erros na tela (ambiente de desenvolvimento)
ini_set('display_startup_erros', 1);   //Exibição de erros que ocorrem durante a inicialização do PHP
ini_set('log_errors', 1);              //Ativa a gravação de erros em um arquivo de log externo
ini_set('error_log', __DIR__ . '/errors/log_' . date('Y-m-d__H-i-s') . '.txt'); //Gravando Logs de erros
error_reporting(E_ALL);  //Reportando todos os tipos de erros

//Capturando erros comuns do PHP (ex: variáveis indefinidas, avisos)
function errorHandler($errno, $errstr, $errfile, $errline)
{
    error_log("Erro [$errno] em $errfile:$errline - $errstr");    //Detalhes do erro (número, arquivo, linha e mensagem) no arquivo de log
    http_response_code(500);
    echo "Ocorreu um erro interno. Tente novamente mais tarde.";  //Mensagem amigável e genérica para o usuário final
    exit;
}

//Função para capturar Exceções; Erros que não foram tratadas com try/catch
function exceptionHandler(Throwable $exception)
{
    $message = sprintf( //Formatando detalhes da exceção
        "Exceção não capturada: %s | Arquivo: %s | Linha: %d",
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );

    //Grava a mensagem e o histórico de onde o erro começou (Trace) no arquivo de log
    error_log($message);
    error_log($exception->getTraceAsString());
    
    http_response_code(500);
    echo "Erro inesperado. Tente novamente."; //Exibe a mensagem amigável para o usuário
    exit;
}

//Função executada sempre que o script PHP termina de rodar (Shutdown).
//Usada aqui para capturar "Erros Fatais" que travam o PHP antes das outras funções agirem.
function shutdownHandler()
{
    $error = error_get_last(); //Pega o último erro que ocorreu antes do script fechar
    
    //Verifica se houve um erro e se ele é do tipo Fatal, Parse (sintaxe), Core ou Compile
    if($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR]))
    {
        error_log("Erro fatal: {$error['message']} em {$error['file']}:{$error['line']}");
        http_response_code(500);
        echo "Erro crítico. Tente novamente.";
        exit;
    }
}

//ATIVAÇÃO DOS MANIPULADORES
set_error_handler('errorHandler');              //Erros do PHP
set_exception_handler('exceptionHandler');      //Exceções não capturadas
register_shutdown_function('shutdownHandler');  //Executada no encerramento do script