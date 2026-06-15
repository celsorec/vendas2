//AJAX
let searchInput = document.querySelectorAll('.input-search input'); //Campo para digitação do código de barras
let resultAjax  = document.querySelector('.result-ajax');           //Elemento para apresentação do resultado Ajax

//Buscando itens por digitação do código de barras, AJAX
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

//Função para exibir resultado das buscas Ajax
function displayItems(responseData)
{
    let html = '';
    if(!responseData.minLenghtAlert) //Se NÃO é a mensagem 'Insira pelo menos 8 dígitos'
    {
        responseData.forEach((element) =>
        {
            html += '<li>';
            Object.entries(element).forEach(([key, value]) =>
            {
                if(key === 'promo') value = (+value).toFixed(2);

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
}