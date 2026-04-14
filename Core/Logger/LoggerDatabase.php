<?php

class LoggerDatabase
{
    //Cria pastas e arquivos de LOG
    public static function register(string $path, string $fileName, array $info): void
    {
        //Cria pastas recursivamente
        if(!is_dir($path))
        {
            mkdir($path, 0777, true);
        }
        
        //Se já existe arquivo JSON, adiciona ao array
        if(file_exists($path.'/'.$fileName.'.json'))
        {
            $jsonFile = file_get_contents($path.'/'.$fileName.'.json');
            $jsonFile = json_decode($jsonFile, true);
            $newInfo  = $jsonFile;
        }

        //Acrescenta as novas informações ao array e salva JSON
        $newInfo[] = $info;
        $newInfo = json_encode($newInfo, JSON_PRETTY_PRINT);
        file_put_contents($path.'/'.$fileName.'.json', $newInfo.PHP_EOL);
    }

    //Acessa pastas e arquivos de LOGS
    public static function read(string $year='', string $month='', string $file=''): mixed
    {
        function accessBy($array)
        {
            //Ordena por pasta/arquivo com modificação mais recente
            usort($array, function($a, $b)
            {
                return filemtime($b) <=> filemtime($a); 
            });

            //Configurando parâmetros de URL
            $folders = [];
            foreach($array as $value)
            {
                $index = basename($value); //Definindo chave do novo array com o nome do último item do caminho
                
                $value = explode('/', $value);
                $value = implode('%s', $value);
                $value = sprintf($value, '&year=', '&month=', '&file='); //Substituindo %s por navegação hierárquica
                
                $folders[$index] = str_replace('Logs', '', $value);
            }
            return $folders;
        }

        //Lista pastas anuais
        if(!$year)
        {
            $search = glob('Logs/*', GLOB_ONLYDIR);
            $search = accessBy($search);
            return $search;
        }

        //Lista pastas mensais, a partir do ano selecionado
        if(!$month)
        {
            $search = glob('Logs/'.$year.'/*', GLOB_ONLYDIR);
            $search = accessBy($search);
            return $search;
        }

        //Apresenta arquivos dentro das pastas de ano e mês selecionados
        if(!$file) 
        {
            $search = glob('Logs/'.$year.'/'.$month.'/*.json');
            $search = accessBy($search);
            return $search;
        }
        else
        {
            //Retorna conteúdo do arquivo selecionado pelo caminha completo
            $files = file_get_contents('Logs/'.$year.'/'.$month.'/'.$file);
            return $files;
        }
    }
}