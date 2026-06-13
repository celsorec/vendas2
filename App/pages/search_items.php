<?php

//PERMISSÃO DE ACESSO (aceus_itens.php)
$aceusPageId = 4;
aceusAllowed($aceusPageId);

$dataFilters = '
data-class="form_items"
data-thead="Código, Nome, Descrição, SQL_ROWID"
data-table="itens"
data-likes="codit, nomit, decit"
data-columns="codit, nomit, decit, SQL_ROWID"';

$output = <<<OUTPUT
<div id='search_items' class='page search_page'>
    <header>
        <h2>Localizar Item</h2>
    </header>
    
    <div class='columns'>
        <div class='fields-group search col-12'>
            <label for='search'>Código, Nome ou Descrição</label>
            <input class='search' name='search' id='search' autocomplete='off' type='text' {$dataFilters} placeholder='O que você procura?'>
            <div class='display-ajax'></div>
        </div>
    </div>
</div>
OUTPUT;

return $output;