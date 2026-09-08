<?php

$settings = json_decode(file_get_contents('settings.json'), true);
$version  = $settings['version'];

//Styles CSS
$styles = [
    'root.css',
    'views.css',
    'elements.css',
    'classes.css',
    'columns.css',

    /* Modules */
    'modules/menu.css',
    'modules/load.css',
    'modules/message.css',
    'modules/update-notify.css',

    /* Views */
    'views/config.css',
    'views/home.css',
    'views/barcode.css',
    'views/orders.css',
    'views/search.css',
    'views/about.css',
    'views/help.css',
    'views/404.css',
    'views/403.css',
    'views/share-app.css'
];

foreach($styles as $style)
{
    echo "<link rel=\"stylesheet\" href=\"assets/css/{$style}?v={$version}\" >\n";
}