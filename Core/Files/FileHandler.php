<?php

class FileHandler
{
    //Cria pastas e arquivos de LOG
    public static function saveFile(string $path, array $fileInfo, string $cpath=''): string
    {
        $finfo = finfo_open(FILEINFO_NONE);  //Abrindo funções de arquivo
        $fpath = explode('/', $path);        //Explodir para separar nome e caminho do arquivo                 
        $fname = array_pop($fpath);          //Recortando nome do arquivo a partir do caminho $fpath
        $fpath = implode('/', $fpath);       //Definindo caminho do arquivo a partir de $fpath

        $extsn = finfo_file($finfo, $fileInfo['files']['tmp_name'], FILEINFO_EXTENSION);  //Extensão do arquivo
        $ftype = finfo_file($finfo, $fileInfo['files']['tmp_name'], FILEINFO_MIME_TYPE);  //Mimetype do arquivo
        $acpts = ['application/pdf'];                                                     //Tipos aceitos

        //Verifica se arquivo é do tipo aceitável: $acpts
        if(!in_array($ftype, $acpts))
        {
            MessageHelper::setMessage('ERRO: Tipo de arquivo não suportado', 'alert');
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit;
        }

        //Cria pastas recursivamente
        if(!is_dir($fpath)) mkdir($fpath, 0777, true);

        //Se arquivo já existe, é renomeado com uniqid()
        if(file_exists('Files/'.$cpath)) rename ('Files/'.$cpath, 'Files/'.(str_replace('.'.$extsn, '', $cpath)).'_'.uniqid().'.'.$extsn);

        //Salvando arquivo
        $data = file_get_contents($fileInfo['files']['tmp_name']);
        file_put_contents($fpath.'/'.strtoupper($fname).'.'.$extsn, $data);

        //Removendo arquivo da memória
        finfo_close($finfo);

        //Retorna caminho e nome do arquivo para chamada da função para evitar a fadiga de escrever uma nova função
        return str_replace('Files/', '', ($fpath.'/'.strtoupper($fname).'.'.$extsn));
    }
}