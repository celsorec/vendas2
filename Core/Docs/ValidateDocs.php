<?php

class ValidateDocs
{
    public static function filterDoc(string $doc): string|null
    {
        //Aceita apenas caracteres numéricos
        $doc = preg_replace('/[^0-9]/', '', $doc);
        
        if(strlen($doc) === 11)
        {
            //Verifica se tem exatamente 11 dígitos | Elimina CPFs inválidos com dígitos repetidos
            if(strlen($doc) != 11 || preg_match('/^(\d)\1{10}$/', $doc))
            {
                return null;
            }

            //Cálculo verificador
            for($t = 9; $t < 11; $t++)
            {
                $sum = 0;
                for($i = 0; $i < $t; $i++)
                {
                    $sum += $doc[$i] * (($t + 1) - $i);
                }

                $checkDigit = ($sum * 10) % 11;
                if($checkDigit == 10)
                {
                    $checkDigit = 0;
                }
                
                if($doc[$t] != $checkDigit)
                {
                    return null;
                }      
            }
        }
        else if(strlen($doc) === 14)
        {
            //Verifica se tem exatamente 14 dígitos | Elimina CNPJs inválidos com dígitos repetidos
            if(strlen($doc) != 14 || preg_match('/^(\d)\1{13}$/', $doc))
            {
                return null;
            }

            //Cálculo verificador
            $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
            $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            for($t = 0; $t < 2; $t++)
            {
                $sum = 0;
                $weights = $t == 0 ? $weights1 : $weights2;
                $length = $t == 0 ? 12 : 13;

                for($i = 0; $i < $length; $i++)
                {
                    $sum += $doc[$i] * $weights[$i];
                }

                $checkDigit = $sum % 11;
                $checkDigit = $checkDigit < 2 ? 0 : 11 - $checkDigit;

                if($doc[$length] != $checkDigit)
                {
                    return null;
                }
            }
        }
        else
        {
            return null;
        }
        return $doc;
    }
}