//AJAX SEARCH
//Campos que acionam o Ajax
const inputSearch = document.querySelectorAll('.search input');
if(inputSearch)
{
    inputSearch.forEach((element, index) =>
    {
        let timer;
        element.addEventListener('input', () =>
        {
            clearTimeout(timer);
            timer = setTimeout(() =>
            {
                let dataFilters = element.dataset;
        
                localStorage.setItem('thead', dataFilters.thead);
                localStorage.setItem('class', dataFilters.class);
                
                searchAjax(element.value, dataFilters, inputSearch[index]);
            }, 500);
        });
    });
}

//Campos ARTIFICIAIS que acionam o Ajax;
//O recurso abaixo pode substituir integralmente o recurso acima
//desde que os campos de buscas tenham a classe 'search_field' 
document.addEventListener('input', function(event)
{
    if(event.target.classList.contains('search_field'))
    {
        let dataFilters = event.target.dataset;

        localStorage.setItem('thead', dataFilters.thead);
        localStorage.setItem('class', dataFilters.class);
        
        searchAjax(event.target.value, dataFilters, event.target);
    }
});

//Ajax
function searchAjax(search, dataFilters, inputSearch)
{
    let ajax = new XMLHttpRequest();
    ajax.onreadystatechange = function()
    {
        if(this.readyState == 4 && this.status == 200)
        {
            let data = JSON.parse(this.responseText);
            localStorage.setItem('ajax', JSON.stringify(data));
            displayAjax(inputSearch);
        }
    }

    //Montando parâmetros URI
    let queryString = 'ajax.php?';
    for (const key in dataFilters)
    {
        queryString += key+'='+encodeURIComponent(dataFilters[key])+'&';
    }

    ajax.open('GET', queryString+'search='+encodeURIComponent(search), true);
    ajax.send();
}

//Converte JSON em localStorage para HTML e exibe
function displayAjax(inputSearch)
{
    //Definindo cabeçalho das tabelas
    let thead = localStorage.getItem('thead');
    thead = thead.split(',');

    let tableHead = '<thead><tr>';
    thead.forEach((element) =>
    {
        tableHead += '<th>'+element.trim()+'</th>';
    });
    tableHead += '</tr></thead>';

    let ajaxResult = localStorage.getItem('ajax');
    if (ajaxResult)
    {
        let parsed = JSON.parse(ajaxResult);
        if (Array.isArray(parsed) && parsed.length)
        {
            let html = []; //Definindo tabela completa
            html.push('<table data-label='+localStorage.getItem('class')+'>'+tableHead+'<tbody>');
            parsed.forEach(function(item)
            {
                html.push('<tr>');
                for (let key in item)
                {
                    html.push('<td class="'+key+'">' + item[key] + '</td>');
                }
                html.push('</tr>');
            });
            html.push('</tbody></table>');
            inputSearch.parentElement.querySelector('.display-ajax').innerHTML = html.join('');
            clickHandler();
        }
        else
        {
            inputSearch.parentElement.querySelector('.display-ajax').innerHTML = '';
        }
    }
}

//FIM DO AJAX UNIVERSAL; A SEGUIR, FUNÇÕES ESPECÍFICAS; Comportamentos individuais
function clickHandler()
{
    let table = document.querySelector('.display-ajax table');
    let label = table.getAttribute('data-label');
    let row   = table.querySelectorAll('tbody tr');

    switch(true)
    {
        case ['form_category', 'form_user', 'form_items', 'form_supplier', 'form_chart'].includes(label): //Páginas de buscas (atualização)
            let idUser = document.querySelectorAll('.display-ajax table tbody .SQL_ROWID');
            idUser.forEach((element) =>
            {
                element.parentElement.addEventListener('click', () =>
                {
                    document.querySelector('#container-load').classList.add('active'); //Animação carregando...
                    setTimeout(() =>
                    {
                        window.location.href = '?page='+label+'&idUpdate='+element.innerText;
                    }, 300);
                });
            });
        break;

        case ['search_field', 'conta_field', 'itens_field', 'categ_field', 'forne_field'].includes(label): //Adiciona resultado ao input
            row.forEach((element) =>
            {
                element.addEventListener('click', () =>
                {
                    let itemsRow = element.querySelectorAll('td');
                    let input    = element.closest('.search').querySelector('input');
                    input.value  = itemsRow[0].innerText+' - '+itemsRow[1].innerText;
                    element.closest('.display-ajax').innerHTML = '';
                });
            });
        break;

        //Válido somente para campo CODIT em form_request.php
        case ['search_field_saldo'].includes(label):
            row.forEach((element) =>
            {
                element.addEventListener('click', () =>
                {
                    let itemsRow = element.querySelectorAll('td');
                    let input    = element.closest('.search').querySelector('input');
                    let tmprc    = document.querySelector('#tmprc'); //Campo para adicionar preço do item para calcular com saldo orçamentário
                    input.value  = itemsRow[0].innerText+' - '+itemsRow[1].innerText;
                    tmprc.value  = itemsRow[2].innerText;
                    element.closest('.display-ajax').innerHTML = '';
                });
            });
        break;
    }
}