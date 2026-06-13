<?php

$output = <<<OUTPUT
<div id='subheader'>
    <button class="btnmenu"></button>
    <span class="welcome">Olá, {$_SESSION['nomus']}!</span>
</div>
OUTPUT;

return $output;