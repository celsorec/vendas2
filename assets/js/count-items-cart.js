/**
 * Contando total de itens no carrinho de compras; destaque em vermelho no menu Vendas
 */
function countItems()
{
    //Quantidade a partir de localStorage
    let itemsCart = JSON.parse(localStorage.getItem('productsList'));
    if(!itemsCart) return;

    let totalMovqt = 0;
    itemsCart.venda1.forEach((element) => totalMovqt += +element.movqt);

    let ordersMenu   = document.querySelector('.orders-menu');                        //Menu carrinho de compras
    let counterItems = document.querySelector('.orders-menu .counter-items.active');  //Contador carrinho de compras
    
    //Verifica se contador já existe
    if(counterItems === null)
    {
        //Se não existe cria e adiciona ao HTML do menu
        counterItems  = '<span class="counter-items active">'+totalMovqt+'</span>';
        counterItems += ordersMenu.innerHTML;

        //Reescrevendo o item do menu: adicionando o contador
        ordersMenu.innerHTML = counterItems;
    }
    else counterItems.innerText = totalMovqt; //Se já existe, apenas atualiza quantidade

    //Se carrinho estiver vazio, remove classe 'active' para ocultar contagem
    if (totalMovqt < 1) ordersMenu.querySelector('.counter-items').classList.remove('active');
}
window.countItems = countItems;
countItems();