<?php
require_once './app/modules/get-select.php';

//Métodos de pagamento
$movncOptions = getSelect(['forpg', 'forpg'], 'forpg', "WHERE SQL_DELETED='F'");
?>

<div id="orders" class="view">
    <div class="container">        
        <form action="">
            <div class="products">
                <header>
                    <h2>PRODUTOS ADICIONADOS</h2>
                </header>
            </div>

            <div class="payment-group">
                <div class="payment-total">
                    <div class="total-label">
                        TOTAL
                    </div>
                    
                    <div class="movde">
                        <span>0.00</span>
                        <input type="hidden" name="movde" value="0.00" >
                    </div>
                </div>

                <div class="payment-buttons">
                    <a href="index.php?view=barcode" class="btn add-item"></a>
                    <span class="btn select-pay"></span>
                </div>
            </div>

            <!-- CHECKOUT -->
            <div class="checkout hidden">
                <div class="header">
                    <span></span><h2>Finalização</h2>
                </div>

                <div class="subheader">
                    <div class="label-total">TOTAL DA VENDA</div>
                    <div class="value-total"></div>
                </div>

                <div class="field-group input-search">
                    <label for="codcl">CLIENTE</label>
                    <div class="group-input codcl">
                        <span></span>
                        <input
                            name="codcl"
                            id="codcl"
                            type="search"
                            inputmode="numeric"
                            minlength="10"
                            maxlength="15"
                            placeholder="Localizar cliente pelo código"
                            data-file="search-ajax-client"
                            data-base="clien01"
                            pattern="[0-9]+\s-\s[A-Za-zÀ-ÿ\s]+"
                            oninvalid="this.setCustomValidity('É preciso localizar e selecionar um cliente')"
                            oninput="this.setCustomValidity('')"
                            required
                        >
                    </div>

                    <ul class="result-ajax">
                        <!--INSERIR LISTA HTML AQUI (Ajax)-->
                    </ul>
                </div>

                <div class="field-group">
                    <label for="movnc">FORMA DE PAGAMENTO</label>
                    <div class="group-input movnc">
                        <span></span>
                        <select name="movnc" id="movnc" required="">
                            <option value="" disabled="" selected="">Selecione</option>
                            <?php foreach($movncOptions as $key => $value) print '<option value="'.$key.'">'.$value.'</option>' ?>
                        </select>
                    </div>
                </div>

                <div class="supergroup">
                    <div class="field-group">
                        <label for="movip">PERCENTUAL DE DESCONTO</label>
                        <div class="group-input movip">
                            <span></span>
                            <input name="movip" id="movip" type="number" inputmode="decimal" step="0.01">
                        </div>
                    </div>

                    <div class="field-group ">
                        <label for="movde">VALOR COM DESCONTO</label>
                        <div class="group-input movde">
                            <span></span>
                            <input name="movde" id="movde" type="number" inputmode="decimal" step="0.01">
                        </div>
                    </div>
                </div>

                <div class="supergroup">
                    <div class="field-group">
                        <label for="fentr">VALOR DA ENTRADA</label>
                        <div class="group-input fentr">
                            <span></span>
                            <input name="fentr" id="fentr" type="number" inputmode="decimal" step="0.01">
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="ftota">RESTANTE</label>
                        <div class="group-input ftota">
                            <span></span>
                            <input name="ftota" id="ftota" type="number" readonly>
                        </div>
                    </div>
                </div>

                <div class="supergroup">
                    <div class="field-group">
                        <label for="fnpre">QUANTIDADE DE PARCELAS</label>
                        <div class="group-input fnpre">
                            <span></span>
                            <input name="fnpre" id="fnpre" type="number" max="24">
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="fcalc">VALOR DA PARCELA</label>
                        <div class="group-input fcalc">
                            <span></span>
                            <input name="fcalc" id="fcalc" type="number" readonly>
                        </div>
                    </div>
                </div>

                <div class="endcheckout">
                    <button class="btn" type="submit">
                        <span class="icon"></span>
                        <span class="text">Concluir Venda</span>
                    </button>
                </div>
            </div>
        </form>

        <div class="password-group">
            <h2>SENHA DE AUTORIZAÇÃO</h2>
            <div class="field-group">
                <span class="lock-icon"></span>
                <input id="password" maxlength="20" type="password">
                <span class="eye-icon"></span>
            </div>

            <div class="field-group">
                <button class="btn-ok">OK</button>
                <button class="btn-cancel">CANCELAR</button>
            </div>
        </div>
    </div>

    <!-- Importado na INDEX -->
    <nav class="mainmenu"><?=$menu;?></nav>
</div>