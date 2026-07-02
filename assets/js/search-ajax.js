//AJAX
function searchAjax(element)
{
    //Banco de dados e tabela selecionados através dos atributos do input
    let table   = element.getAttribute('data-base');
    let columns = element.getAttribute('name');
    let query   = element.value;
    let file    = element.dataset.file; //Recebe aquivo que vai tratar o assunto: cliente, produto...

    //Retorna uma Promise resolvida com um array vazio; Evitar console de erro quando input fica vazio
    if(query.length <= 0) return Promise.resolve([]);

    //Função Ajax
    return fetch('app/modules/'+file+'.php?table='+table+'&columns='+columns+'&q='+encodeURIComponent(query))
    .then(response =>
    {
        if(!response.ok) throw new Error('Erro na resposta do servidor');
        return response.json();
    })
    .catch(error =>
    {
        console.error('Erro na requisição AJAX:', error);
        return [];
    });
}
window.searchAjax = searchAjax;