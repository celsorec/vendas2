<?php

$settings = json_decode(file_get_contents('settings.json'), true);
$version  = $settings['version'];

//Scripts JS em ordem de execução
$scripts = [
    'active-menus.js',
    'message-handler.js',
    'load.js',
    'barcode.js',
    'search-ajax.js',
    'search-add-products.js',
    'search-select-client.js',
    'load-cart.js',
    'update-cart.js',
    'count-items-cart.js',
    'data-settings-checkout.js',
    'share-app.js',
    'update-notify.js'
];

foreach($scripts as $script)
{
    echo "<script src=\"assets/js/{$script}?v={$version}\" defer></script>\n";
}

/*
document.addEventListener('contextmenu', function(e)
{
    e.preventDefault();
});
*/
