<?php

//PERMISSÃO DE ACESSO (aceus_items.php)
$aceusPageId = 2;
aceusAllowed($aceusPageId);

$dataFilters = '
data-class="form_user"
data-thead="Código, Nome, CPF, Fone, SQL_ROWID"
data-table="usuar"
data-likes="codus, nomus, cpfus, fonus"
data-columns="codus, nomus, cpfus, fonus, SQL_ROWID"';

$output = <<<OUTPUT
<div id='search_user' class='page search_page'>
    <header>
        <h2>Localizar Usuário</h2>
    </header>
    
    <div class='columns'>
        <div class='fields-group search col-12'>
            <label for='search'>Código, Nome, CPF ou Telefone</label>
            <input class='search' name='search' id='search' autocomplete='off' type='text' {$dataFilters} placeholder='Quem você procura?'>
            <div class='display-ajax'></div>
        </div>
    </div>
</div>
OUTPUT;

return $output;