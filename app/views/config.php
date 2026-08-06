<?php
//Obtendo localização da loja através do domínio
$location = explode(':', $_SERVER['HTTP_HOST'])[0];
$location = explode('.', $location);

//Verificando se há subdomínio
if(count($location) > 2) $location = htmlspecialchars($location[0], ENT_QUOTES, 'UTF-8');

//Definindo banco de dados e logotipo; combinando URL e ARRAY
$locationdb =
[
    //Ideal
    "ideal_buriticupu"      => "bd_ideal_buriti",
    "ideal_repartimento"    => "bd_ideal_rp",
    "ideal_caxias"          => "bd_ideal_ca",
    "ideal_grajau"          => "bd_ideal_gr",
    "ideal_santaluzia"      => "bd_ideal_sl",
    "ideal_bacabal"         => "bd_ideal_ba",
    "ideal_santarem"        => "bd_ideal_santarem",
    "ideal_presidentedutra" => "bd_ideal_pd",
    "ideal_altamira"        => "bd_ideal_al",
    
    
    "ideal_acailandia"   => "bd_ideal_ac",
    "ideal_imperatriz"   => "bd_ideal_im",
    "ideal_balsas"       => "bd_ideal_bs",

    //Maruzi
    "maruzi_grajau"    => "bd_maruzi_gr",
    "maruzi_altamira"  => "bd_ideal_parceira"

    //Sports
];

if(isset($locationdb[$location])) $_SESSION['dbname'] = $locationdb[$location];
//A fazer: corrigir esse HTML manual com echo
else echo '<div class="alert message" style="left: 0px; opacity: 1; visibility: visible;"><span class="icon-alert">Erro: Verifique o link de acesso.</span><button class="close">x</button></div>';
//$_SESSION['dbname'] = 'bd_ideal_buriti';
?>

<div id="config">
    <form method="POST" action="auth.php">
        <main class="container">
            <section id="bag-logo">
                <img src="app/media/images/bag-logo.svg" alt="App de Vendas">
            </section>

            <section id="select-user">
                <label for="usercode" class="subtitle">DIGITE SEU CÓDIGO</label>
                <div class="group-input usercode">
                    <span></span>
                    <input type="number" inputmode="numeric" id="usercode" name="usercode" value="" required>
                </div>
            </section>
            
            <footer>
                <button class="btn" type="submit"><span class="icon"></span><span class="text">Prosseguir</span></button>
            </footer>
        </main>
    </form>
</div>