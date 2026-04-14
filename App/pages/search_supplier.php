<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 6;
aceusAllowed($aceusPageId);

$dataFilters = '
data-class="form_supplier"
data-thead="Código, Razão Social, Nome Fantasia, CNPJ / CPF, SQL_ROWID"
data-table="forne"
data-likes="codfo, razfo, fanfo, docfo"
data-columns="codfo, razfo, fanfo, docfo, SQL_ROWID"';

$output = <<<OUTPUT
<div id='search_supplier' class='page search_page'>
    <header>
        <h2>Localizar Fornecedor</h2>
    </header>
    
    <div class='columns'>
        <div class='fields-group search col-12'>
            <label for='search'>Código, Nome ou CNPJ / CPF</label>
            <input class='search' name='search' id='search' autocomplete='off' type='text' {$dataFilters} placeholder='Que fornecedor você procura?'>
            <div class='display-ajax'></div>
        </div>
    </div>
</div>
OUTPUT;

return $output;