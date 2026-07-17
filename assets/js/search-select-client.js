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
                html += '<li class="list-items-ajax">';
                Object.entries(element).forEach(([key, value]) =>
                {
                    html += '<span class="'+key+'">'+value+'</span>';
                });
                html += '</li>';
            });
        }
        else //Se É a mensagem 'Insira pelo menos 3 dígitos'
        {
            html += '<li class="empty-list"><span class="alert">'+responseData.minLenghtAlert+'</span></li>';
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
        let clientsInfo = document.querySelectorAll('.result-ajax li.list-items-ajax');
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
                searchInput.dispatchEvent(new Event('input', {bubbles: true}));
        
                //Ocultar lista ajax quando cliente é selecionado
                element.parentElement.classList.add('hidden');
            });
        });
    }
}