<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 1;
aceusAllowed($aceusPageId);

$dataFilters = '
data-class="form_category"
data-thead="Código, Nome, Descrição, SQL_ROWID"
data-table="categ"
data-likes="codca, nomca, desca"
data-columns="codca, nomca, desca, SQL_ROWID"';

$output = <<<OUTPUT
<div id='search_category' class='page search_page'>
    <header>
        <h2>Localizar Categoria</h2>
    </header>
    
    <div class='columns'>
        <div class='fields-group search col-12'>
            <label for='search'>Nome ou Código da Categoria</label>
            <input class='search' name='search' id='search' autocomplete='off' type='text' {$dataFilters} placeholder='Qual categoria de itens você procura?'>
            <div class='display-ajax'></div>
        </div>
    </div>
</div>
OUTPUT;

return $output;