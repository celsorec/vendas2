<?php

//Lista de lojas nas configurações iniciais
if(!isset($_SESSION['storesdb']))
{
    //Obter nomes dos bancos de dados online
    function getStores()
    {
        $dbOnline = 'https://idealmagazineoficial.com.br/assets/storesdb.json';

        //Substitui temporariamente o manipulador de erros do PHP
        //Função vazia → ignora qualquer warning/notice
        set_error_handler(function() {});

        $dbOnline = file_get_contents($dbOnline);

        //Restaura o comportamento normal de erros do PHP
        restore_error_handler();

        if($dbOnline === false) return 'ERRO: Verifique sua conexão com a internet.';
        return json_decode($dbOnline, true);
    }

    //Salva localmente para evitar necessidade de nova conexão com a internet
    $_SESSION['storesdb'] = getStores();
}

//Só prossegue se obteve os dados online: array
if(!is_array($_SESSION['storesdb']))
{   
    echo $_SESSION['storesdb'];   //Exibe mensagem de erro
    unset($_SESSION['storesdb']); //Deleta mensagem de erro para tentar nova conexão online para obter nomes dos bancos de dados
    exit;    
}

//Nomes dos bancos de dados das lojas
$storesdb = $_SESSION['storesdb'];
?>
<div id="config">
    <form method="POST" action="auth.php">
        <main class="container">
            <header>
                <h1><span class="icon"></span><span class="text">CONFIGURAÇÃO INICIAL</span></h1>
            </header>

            <section id="select-store">
                <h3>SELECIONAR LOJA</h3>
                <div class="stores">
                    <input type="radio" id="ideal" name="store" value="Ideal Magazine" required>
                    <label for="ideal" class="btn-store ideal"></label>

                    <input type="radio" id="maruzi" name="store" value="Maruzi" required>
                    <label for="maruzi" class="btn-store maruzi"></label>
                </div>
            </section>

            <section id="select-city">
                <label for="location" class="subtitle">SELECIONAR LOCALIZAÇÃO</label>

                <div class="group-input location">
                    <span></span>
                    <select id="location" name="location" required>
                        <option value="" disabled selected>Selecione uma das lojas acima</option>
                        <!--INSERIR HTML OPTIONS AQUI-->
                    </select>
                </div>
            </section>

            <section id="select-user">
                <label for="usercode" class="subtitle">INFORME SEU CÓDIGO DE ACESSO</label>
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

<script>
//Armazenando lojas em localStorage
const storesdb = <?php echo json_encode($_SESSION['storesdb']); ?>;
localStorage.setItem('storesdb', JSON.stringify(storesdb));

//Elementos para usar e manipular
const radios = document.querySelectorAll('input[name="store"]');
const select = document.getElementById('location');

//Quando clique em logotipo de uma das lojas...
radios.forEach(radio =>
{
    radio.addEventListener('change', function()
    {
        const store = this.value;
        select.innerHTML = '<option value="" disabled selected>Selecione</option>';

        if(storesdb[store])
        {
            //Altera a lista de lojas e seus bancos de dados
            Object.entries(storesdb[store]).forEach(([key, value]) =>
            {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = value;

                select.appendChild(option);
            });
        }
    });
});
</script>