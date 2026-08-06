<?php
//Obtenção do IP do cliente
$clientIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
$accessAllowed = false;

//Função para consultar o IP do servidor com cache temporário
function getServerIpCache($url, $fileName)
{
    $cachePath = sys_get_temp_dir() . '/' . $fileName;
    $expirationTime = 300; //5 minutos

    if(file_exists($cachePath) && (time() - filemtime($cachePath) < $expirationTime)) return file_get_contents($cachePath);

    //Requisição via cURL com controle de timeout e DNS
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);             //Define a URL de destino da requisição
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //Configurado como true, faz o curl_exec() retornar o conteúdo como string em vez de exibi-lo tela
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);     //Define o tempo limite máximo (em segundos) para conseguir estabelecer a conexão inicial com o servidor.
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);            //Tempo limite máximo (em segundos) para a execução e conclusão total da requisição.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Configurado como false, desativa a verificação do certificado SSL do destino (evita falhas de conexão por certificado inválido ou ausente).

    $ip = curl_exec($ch);
    $error = curl_errno($ch);

    if(!$error && !empty($ip))
    {
        $ip = trim($ip);
        file_put_contents($cachePath, $ip);
        return $ip;
    }

    //Retorno do cache existente caso a consulta DNS falhe
    if(file_exists($cachePath)) return file_get_contents($cachePath);
    return '';
}

if(filter_var($clientIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
{   //Verificação para conexões IPv6
    $serverIpV6 = getServerIpCache('https://api6.ipify.org', 'server_ip_v6.txt');

    if(!empty($serverIpV6))
    {
        $clientPrefix = implode(':', array_slice(explode(':', $clientIp), 0, 4));
        $serverPrefix = implode(':', array_slice(explode(':', $serverIpV6), 0, 4));
        if($clientPrefix === $serverPrefix) $accessAllowed = true;
    }
}
else //Verificação para conexões IPv4
{
    $serverIpV4 = getServerIpCache('https://api4.ipify.org', 'server_ip_v4.txt');
    if($clientIp === $serverIpV4) $accessAllowed = true;
}