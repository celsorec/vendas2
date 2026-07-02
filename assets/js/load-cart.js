let htmlProdu = document.querySelector('.products');

if(htmlProdu)
{
    function loadCart()
    {
        let html = '<header><h2>PRODUTOS ADICIONADOS</h2></header>';
        let itemsCart = localStorage.getItem('productsList');
        itemsCart = JSON.parse(itemsCart);
        
        if(!itemsCart) return;
        
        itemsCart.venda1.forEach((element, index) =>
        {
            html += `
                <div class="product">
                    <div class="top-group">
                        <div class="nompr">
                            <input type="text" name="nompr[]" value="${element.nompr}" data-index="${index}" readonly>
                        </div>

                        <div class="codpr gragr">
                            <input type="text" name="codpr[]" value="${element.codpr}" data-index="${index}" readonly>
                            <input type="text" name="gragr[]" value="${element.gragr}" data-index="${index}" readonly>
                        </div>
                    </div>

                    <!-- OCULTOS -->
                    <input type="hidden" name="gragr[]" value="${element.prcpr}" data-index="${index}">
                    
                    <div class="bottom-group">
                        <div class="movqt">
                            <span class="minus"></span>
                            <input type="number" name="movqt[]" value="${element.movqt}" min="1" inputmode="numeric" data-index="${index}" readonly>
                            <span class="plus"></span>
                        </div>

                        <div class="${(element.hasOwnProperty('venpr')?'venpr':'promo')}">
                            <span class="subtt" data-index="${index}">${element.subtt}</span>
                            <input type="hidden" name="${(element.hasOwnProperty('venpr')?'venpr':'promo')}[]" value="${+(element.venpr ?? element.promo)}" data-index="${index}">
                        </div>
                        <div class="delete">
                            <span class="btn" data-index="${index}"></span>
                        </div>
                    </div>
                </div>
            `;
        });
        htmlProdu.innerHTML = html;

        //Exportada de update-cart.js para evitar assincronismo
        if(typeof window.updateCart === 'function') window.updateCart();
    }
    loadCart();
    window.loadCart = loadCart;
}