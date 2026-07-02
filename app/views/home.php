<div id="home" class="view">
    <div class="container">
        <!-- Boas-vidas e iniciar venda -->
        <div class="logo <?=$_SESSION['logos']?>"></div>

        <section class="init-order">
            <div class="welcome">Olá, <?=ucwords(strtolower($_SESSION['nomve']))?>!</div>

            <a href="index.php?view=barcode" class="btn addprod load"><span class="icon"></span> <span class="text">Adicionar Produto</span></a>

            <div class="info">
                Para iniciar uma venda, clique no botão 'Adicionar Produto'.
            </div>
        </section>
    </div>

    <!-- Importado na INDEX -->
    <nav class="mainmenu"><?=$menu;?></nav>
</div>