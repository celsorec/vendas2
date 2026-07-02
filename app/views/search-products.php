<div id="search" class="view">
    <div class="container">
        <header>
            <h2>LOCALIZAR E ADICIONAR ITENS</h2>
        </header>

        <div class="input-search">
            <label for="nompr">Adicionar produto</label>
            
            <div class="group-input nompr">
                <span></span>
                <input name="nompr" id="nompr" type="search" inputmode="numeric" maxlength="10" placeholder="Digite o código de barras" data-file="search-ajax-products" data-base="produ01">
            </div>

            <ul class="result-ajax">
                <!--INSERIR LISTA HTML AQUI-->
            </ul>
        </div>
    </div>
    <!-- Importado na INDEX -->
    <nav class="mainmenu"><?= $menu; ?></nav>
</div>