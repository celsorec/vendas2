<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 9;
aceusAllowed($aceusPageId);

$dataFilters = '
data-class="form_chart"
data-thead="Classificação, Nome, SQL_ROWID"
data-table="conta"
data-likes="clapl, nompl"
data-columns="clapl, nompl, SQL_ROWID"';

$output = <<<OUTPUT
<div id='search_chart' class='page search_page'>
    <header>
        <h2>Localizar Plano de Contas</h2>
    </header>
    
    <div class='columns'>
        <div class='fields-group search col-12'>
            <label for='search'>Classificação ou Nome</label>
            <input class='search' name='search' id='search' autocomplete='off' type='text' {$dataFilters} placeholder='Qual plano de contas você procura?'>
            <div class='display-ajax'></div>
        </div>
    </div>
</div>
OUTPUT;

return $output;