//LOCALIZAR PRODUTOS; LISTAR LOCALIZADOS; ADICIONAR A LOCALSTORAGE
let searchInput = document.querySelectorAll('.input-search input#nompr'); //Campo para digitação do código de barras
let resultAjax  = document.querySelector('.result-ajax');                 //Elemento para apresentação do resultado Ajax

/**
 * Buscando itens por digitação do código de barras, AJAX
 */
searchInput.forEach((element) =>
{
    element.addEventListener('input', () =>
    {
        //Função searchAjax; arquivo search-ajax.js
        window.searchAjax(element).then((responseData) =>
        {
            displayItems(responseData);
        });
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
        //Cria lista de produtos encontrados via Ajax
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
    toLocalStorage();
}

/**
 * Adicionando itens a localStorage para o carrinho de compras
 */

//Para gravar produtos em localStorage
let productsList = {venda1: []};

//Itens em localStorage
let storedProducts = localStorage.getItem('productsList');
if(storedProducts) //Se há itens em localStorage adiciona-os a ${productsList}
{
    storedProducts = JSON.parse(storedProducts); //Converte para JSON
    storedProducts = storedProducts.venda1;      //Seleciona venda pelo nome

    //Cada item encontrado na venda é adicionado à lista
    Object.entries(storedProducts).forEach((product) => 
    {
        productsList.venda1.push(product[1]);
    });
}

/**
 * Adicionando novos itens ao click
 */
function toLocalStorage()
{
    //Lista de produtos buscados via Ajax
    let productsInfo = document.querySelectorAll('.result-ajax li');
    productsInfo.forEach((element) =>
    {
        //Revela lista ajax quando produtos são buscados
        element.parentElement.classList.remove('hidden');

        //Cada produto (li) e suas informações (span)
        element.addEventListener('click', () =>
        {
            //Atributos do produto atual
            let items = element.querySelectorAll('span');
            let currentProduct = {};
            items.forEach((span) =>
            {
                //Obtendo informações dos atributos do produto
                let classAttribute = span.getAttribute('class');
                let contentItem    = span.innerText;

                //Combinando informações e atributos do produto clicado para adicionar à lista [objeto JS]
                currentProduct[classAttribute] = contentItem;
            });
            productsList.venda1.push(currentProduct); //Adicionando produtos à lista

            //Ocultar lista ajax quando produto é selecionado
            element.parentElement.classList.add('hidden');

            //Limpando INPUT após click em um dos produtos listados
            searchInput.forEach((element) => element.value = '');

            //Ocultando o Checkout quando produto é adicionado ao carrinho de compras para mostrar produtos
            localStorage.removeItem('classCheckout'); //Veja mais em data-settings-checkout.js

            //Adiciona itens a localStorage
            localStorage.setItem('productsList', JSON.stringify(productsList));
            window.countItems();
        });
    });
}