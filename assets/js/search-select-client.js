//LOCALIZAR CLIENTE; LISTAR LOCALIZADOS; ADICIONAR AO CAMPO
let searchInput = document.querySelector('.input-search input#codcl'); //Campo para digitação do código do cliente
let resultAjax  = document.querySelector('.result-ajax');              //Elemento para apresentação do resultado Ajax

if(searchInput)
{
    /**
     * Buscando por digitação do código do cliente, AJAX
     */
    searchInput.addEventListener('input', () =>
    {
        //Função searchAjax; arquivo search-ajax.js
        window.searchAjax(searchInput).then((responseData) =>
        {
            displayItems(responseData);
        });
    });
    
    /**
     * Função para exibir resultado das buscas Ajax
     */
    function displayItems(responseData)
    {
        let html = '';
        if(!responseData.minLenghtAlert) //Se NÃO é a mensagem 'Insira pelo menos 8 dígitos'
        {
            //Cria lista de clientes encontrados via Ajax
            responseData.forEach((element) =>
            {
                html += '<li>';
                Object.entries(element).forEach(([key, value]) =>
                {
                    html += '<span class="'+key+'">'+value+'</span>';
                });
                html += '</li>';
            });
        }
        else //Se É a mensagem 'Insira pelo menos 8 dígitos'
        {
            html += '<li><span class="alert">'+responseData.minLenghtAlert+'</span></li>';
        }
        resultAjax.innerHTML = html;
        toInput();
    }
    
    /**
     * Adicionando cliente ao input e a localStorage('checkoutItems.codcl')
     */
    function toInput()
    {
        //Lista de clientes localizados via Ajax
        let clientsInfo = document.querySelectorAll('.result-ajax li');
        clientsInfo.forEach((element) =>
        {
            //Revela lista ajax quando clientes são localizados
            element.parentElement.classList.remove('hidden');
    
            //Cada cliente (li) e suas informações (span)
            element.addEventListener('click', () =>
            {
                //Atributos do cliente clicado
                let items  = element.querySelectorAll('span');
                let selectClient = '';
                items.forEach((span, index) =>
                {
                    //Juntando CODCL + NOMCL -> Pattern válido para o input do cliente
                    if(index === 0) selectClient += span.innerText+ ' - ';
                    if(index === 1) selectClient += span.innerText;
                });
    
                //Adicionando valor ao input cliente imediatamente
                searchInput.value = selectClient;
    
                //Dados do checkout em localStorage (semelhante a data-settings-checkout.js)
                let valuesInput = JSON.parse(localStorage.getItem('checkoutItems')); 
    
                //Verifica se há valores em localStorage ao iniciar o objeto
                let checkoutItems = {venda1: {}};
                if(valuesInput !== null) checkoutItems = {venda1: valuesInput.venda1};
    
                //Salvar código e nome do cliente em localStorage (checkoutItems.codcl)
                checkoutItems.venda1[searchInput.name] = selectClient;
                localStorage.setItem('checkoutItems', JSON.stringify(checkoutItems));
    
                //Ocultar lista ajax quando cliente é selecionado
                element.parentElement.classList.add('hidden');
            });
        });
    }
}