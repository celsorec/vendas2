//ATUALIZAR INFORMAÇÕES NO CARRINHO DE COMPRAS

function updateCart()
{
    //Carrinho de compras em localStorage
    let itemsCart = JSON.parse(localStorage.getItem('productsList'));

    //Botões incrementar/decrementar quantidade
    let movqtPlus  = document.querySelectorAll('.movqt .plus');
    let movqtMinus = document.querySelectorAll('.movqt .minus');

    /**
     * Incrementando quantidade e modificando valores dos itens no carrinho de compras (qtd * preço)
     */
    movqtPlus.forEach((element) =>
    {
        element.addEventListener('click', () =>
        {
            //Campo quantidade -> movqt
            let movqtInput      = element.parentElement.querySelector('input');
            let movqtInputValue = movqtInput.value;
            
            //Incrementando e atualizando no formulário
            movqtInputValue++;
            movqtInput.value = movqtInputValue;

            //Para disparar 'change' artificial(sem interação direta do usuário)
            movqtInput.dispatchEvent(new Event('change'));

            //Campo OCULTO subtotal -> venpr OU promo
            let venprInputValue = element.parentElement.parentElement.querySelector('.venpr input, .promo input').value;
            
            //Recalculando quantidade * preço
            let subtt       = element.parentElement.parentElement.querySelector('.subtt');
            subtt.innerText = (venprInputValue * movqtInputValue).toFixed(2);
            
            //Atualizando em localStorage
            itemsCart.venda1[subtt.dataset.index].subtt = subtt.innerText;      //Subtotal
            itemsCart.venda1[movqtInput.dataset.index].movqt = movqtInputValue; //Quantidade
            localStorage.setItem('productsList', JSON.stringify(itemsCart));
        });
    });

    /**
     * Decrementando quantidade e modificando valores dos itens no carrinho de compras (qtd * preço)
     */
    movqtMinus.forEach((element) =>
    {
        element.addEventListener('click', () =>
        {
            //Campo quantidade -> movqt
            let movqtInput      = element.parentElement.querySelector('input');
            let movqtInputValue = movqtInput.value;
            
            if(movqtInputValue > 1) //Impedindo quantidade ZERO
            {
                //Decrementando e atualizando no formulário
                movqtInputValue--;
                movqtInput.value = movqtInputValue;

                //Para disparar 'change' artificial(sem interação direta do usuário)
                movqtInput.dispatchEvent(new Event('change'));

                //Campo OCULTO subtotal -> venpr OU promo
                let venprInputValue = element.parentElement.parentElement.querySelector('.venpr input, .promo input').value;
                
                //Recalculando quantidade * preço
                let subtt       = element.parentElement.parentElement.querySelector('.subtt');
                subtt.innerText = (venprInputValue * movqtInputValue).toFixed(2);
                
                //Atualizando em localStorage
                itemsCart.venda1[subtt.dataset.index].subtt = subtt.innerText;      //Subtotal
                itemsCart.venda1[movqtInput.dataset.index].movqt = movqtInputValue; //Quantidade
                localStorage.setItem('productsList', JSON.stringify(itemsCart));
            }
        });
    });

    /**
     * DELETANDO DO CARRINHO DE COMPRAS
     */
    let deleteBtn = document.querySelectorAll('.product .delete .btn');
    deleteBtn.forEach((element) =>
    {
        element.addEventListener('click', () =>
        {
            //Obtendo índice compatível com item salvo em localStorage
            let dataIndex = element.dataset.index;
            
            //Removendo produto de localStorage, conforme índice acima
            itemsCart.venda1.splice(dataIndex, 1);

            //Atualizando lista de produtos
            localStorage.setItem('productsList', JSON.stringify(itemsCart));

            //Renderizando carrinho de compras (considerando item removido)
            window.loadCart();
        });
    });

    /**
     * Calculando o valor total da compra
     */
    let subTotals  = document.querySelectorAll('.subtt');    //Subtotal de cada produto no carrinho de compras
    let movdeSpan  = document.querySelector('.movde span');  //Total (da compra) visível (SPAN)
    let movdeInput = document.querySelector('.movde input'); //Total (da compra) invisível (INPUT)

    //Adicionar valor calculado ao campo MOVDE
    function movdeValues()
    {
        let movdeText = 0;
        subTotals.forEach((element) => //Somando subtotais dos produtos
        {
            movdeText += +element.textContent;
        });
        movdeText = movdeText.toFixed(2); //Apenas duas casas decimais depois da vírgula
        movdeInput.value    = movdeText;  //Adicionando valor ao input oculto
        movdeSpan.innerText = movdeText;  //Adicionando valor ao span visível

        //Funções Exportadas para evitar assincronismo
        if(typeof window.countItems       === 'function') window.countItems();       //de update-cart.js 
        if(typeof window.setValueCheckout === 'function') window.setValueCheckout(); //de data-settings-checkout.js
    }
    movdeValues();

    //Observando mudanças em cada SUBTT para acionar função movdeValues()
    let observerSubtt = new MutationObserver(() => {movdeValues()});
    subTotals.forEach((element) => {observerSubtt.observe(element, {childList: true})});
}
window.updateCart = updateCart; //Exportada para ser usada em load-cart.js
if(document.querySelectorAll('.subtt').length > 0) updateCart();