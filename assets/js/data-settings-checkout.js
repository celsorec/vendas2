/**
 * GERENCIANDO CHECKOUT
 */
let selectPay = document.querySelector('.select-pay');            //Botão para revelar checkout
let checkout  = document.querySelector('.checkout');              //Checkout
let btnBack   = document.querySelector('.checkout .header span'); //Botão para ocultar checkout

if(checkout)
{
    /**
     * Revelando | Ocultando checkout -> classe em localStorage
     */

    //Checando se há classe definida em localStorage
    let classCheckout = localStorage.getItem('classCheckout');
    if(classCheckout === null) localStorage.setItem('classCheckout', 'checkout hidden');

    //Adiciona classe que está salva em localStorage
    //checkout.setAttribute('class', localStorage.getItem('classCheckout'));

    //Oculta checkout, conforme classe
    selectPay.addEventListener('click', () => 
    {
        localStorage.setItem('classCheckout', 'checkout');
        checkout.setAttribute('class', localStorage.getItem('classCheckout'));
    });

    //Revela checkout, conforme classe
    btnBack.addEventListener('click', () => 
    {
        localStorage.setItem('classCheckout', 'checkout hidden');
        checkout.setAttribute('class', localStorage.getItem('classCheckout'));
        
        //Limpa campos e localStorage (checkoutItems); pode ter havido alterações na compra, já que o vendedor saiu do checkout
        fieldsAll.forEach((element) => element.value = ''); //Limpa todos os campos
        localStorage.removeItem('checkoutItems');           //Remove de localStorage
    });

    /**
     * Transportando valor total da compra para Checkout
     */
    function setValueCheckout()
    {        
        let movdeValue = JSON.parse(localStorage.getItem('productsList')); //Buscando dados em localStorage
        if(!movdeValue) return;                                            //Evitando erros no console

        //Calculando total da compra
        let sumSubtt = 0;
        movdeValue.venda1.forEach((element) => sumSubtt += +element.subtt);

        //Exibindo total da compra
        let totalValue = document.querySelector('.value-total');
        totalValue.innerText = 'R$ '+sumSubtt.toFixed(2);
    }
    window.setValueCheckout = setValueCheckout; //Importada no arquivo update-cart.js
    setValueCheckout();

    /**
     * Salvando dados do Campos do Checkout em localStorage e recuperado-os
     */
    let fieldsAll   = document.querySelectorAll('.checkout input, .checkout select'); //Inputs e Selects
    let valuesInput = JSON.parse(localStorage.getItem('checkoutItems'));              //Dados em localStorage

    //Verifica se há valores em localStorage ao iniciar o objeto
    let checkoutItems = {venda1: {}};
    if(valuesInput !== null) checkoutItems = {venda1: valuesInput.venda1};

    //Eventos salvam valores do checkout em localStorage
    let eventsListener = ['input', 'change'];

    //Salvando valores em localStorage e recuperando-os
    fieldsAll.forEach((element) => 
    {
        eventsListener.forEach((event) =>
        {
            element.addEventListener((event), () =>
            {
                //Chamando função Aplicar Desconto
                if(element.name === 'movip') applyDiscount('movip');
                if(element.name === 'movde') applyDiscount('movde');

                checkoutItems.venda1[element.name] = element.value;                   //Lê informações digitadas e adiciona-as ao objeto
                localStorage.setItem('checkoutItems', JSON.stringify(checkoutItems)); //Salva informações do input em localStorage
            });
        });

        //Campos recebem valor correspondente recuperado de localStorage ou fica em branco
        element.value = (checkoutItems.venda1[element.name] !== undefined) ? valuesInput.venda1[element.name] : '';
    });

    /**
     * Aplicando desconto
     */
    function applyDiscount(inputActive)
    {
        //Se há produtos em localStorage
        let productsList = JSON.parse(localStorage.getItem('productsList'));
        if(!productsList) return;

        let movdeInput = document.querySelector('input#movde'); //Campo valor final com desconto
        let movipInput = document.querySelector('input#movip'); //Campo de percentual de desconto

        //PRODUTOS COM PREÇOS PROMOCIONAIS -> EXCLUIR DO DESCONTO
        let promoInput = document.querySelectorAll('.promo input');
        let promoValue = 0;
        promoInput.forEach((element) =>
        {
            let movqtValue = element.parentElement.parentElement.querySelector('.movqt input').value;
            promoValue += +element.value * +movqtValue;
        });

        //PRODUTOS COM PREÇOS não PROMOCIONAIS -> APLICAR DESCONTO
        let venprInput = document.querySelectorAll('.venpr input'); 
        let venprValue = 0;
        venprInput.forEach((element) =>
        {
            let movqtValue = element.parentElement.parentElement.querySelector('.movqt input').value;
            venprValue += +element.value * +movqtValue;
        });

        //Somando subtotais dos produtos e preenchendo campo MOVDE com desconto
        let totalMovde = 0;
        productsList.venda1.forEach((element) => totalMovde += +element.subtt);

        if(inputActive === 'movip') //Se alteração é no campo percentual de desconto
        {
            if(movipInput.value > 0)
            {
                let movipValue = +movipInput.value.replace(',', '.');              //Lendo valor e ajustando separador de casa decimal
                totalMovde = +totalMovde - promoValue;                             //Subtraindo valores de produtos com preços promocionais do total da compra
                movdeInput.value = (totalMovde - (totalMovde / 100) * movipValue); //Calculando valor com desconto aplicado   
                movdeInput.value = (+movdeInput.value + promoValue).toFixed(2);    //Adicionando valores de produtos com preços promocionais ao total da compra

                checkoutItems.venda1['movde'] = movdeInput.value;                  //Atualizando valor com desconto em localStorage
                activePassword(movipValue);                                        //Função ativa container da senha para descontos especiais
            }
            else //Se campo é igual a 0 ou ficar em branco
            {
                movipInput.value = '';
                movdeInput.value = '';
                checkoutItems.venda1['movde'] = '';
            }
        }
        else //Se alteração é no campo com o valor
        {
            if(movdeInput.value > 0)
            {
                let movdeValue = +movdeInput.value.replace(',', '.');                         //Lendo valor e ajustando separador de casa decimal
                let venprValueDiscount = movdeValue - promoValue;                             //Subtraindo valores de produtos promocionais do total da compra
                let percentDiscount = ((venprValue - venprValueDiscount) / venprValue) * 100; //Verificando qual o percentual de desconto aplicado

                checkoutItems.venda1['movip'] = percentDiscount.toFixed(2);                   //Atualizando percentual de desconto em localStorage
                movipInput.value = isNaN(percentDiscount) ? '0' : percentDiscount.toFixed(2); //Preenchendo campo com percentual de desconto aplicado
                activePassword(percentDiscount);                                              //Função ativa container da senha para descontos especiais
            }
            else //Se campo é igual a 0 ou ficar em brancos
            {
                movipInput.value = '';
                movdeInput.value = '';
                checkoutItems.venda1['movip'] = '';
                activePassword(0); //Para ocultar container da senha
            }
        }
        //Limpando sob cascata campos no HTML e em localStorage quando desconto é alterado
        fentrInput.value = '';              //Valor da entrada
        ftotaInput.value = '';              //Restante
        checkoutItems.venda1['fentr'] = '';
        checkoutItems.venda1['ftota'] = '';

        fnpreInput.value = '';              //Qtd. de parcelas
        fcalcInput.value = '';              //Valor da parcela
        checkoutItems.venda1['fnpre'] = ''; 
        checkoutItems.venda1['fcalc'] = '';    
    }

    /**
     * Ativa/desativa container da senha para descontos especiais
     */
    function activePassword(movipValue)
    {
        let passwordGroup = document.querySelector('.password-group');
        let descoAtob     = +(atob(localStorage.getItem('desco'))); //Desconto definido no GControl

        if(movipValue > descoAtob) passwordGroup.classList.add('active');
        else passwordGroup.classList.remove('active');
    }
    window.activePassword = activePassword;
    activePassword(+checkoutItems.venda1['movip']); //Em caso de recarregamento da página, verifica desconto aplicado

    /**
     * Alterando o tipo do campo de senha (password || text)
     */
    let passwordInput = document.querySelector('.password-group #password');
    let eyeIconButton = document.querySelector('.eye-icon');
    eyeIconButton.addEventListener('click', () => passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password');

    /**
     * Autorizando com senha descontos acima do padrão
     */
    let passwordGroup = document.querySelector('.password-group');  //Container da senha
    let btnOKPassword = document.querySelector('.btn-ok');          //Botão OK
    let btnCancelPass = document.querySelector('.btn-cancel');      //Botão Cancela
    let senliAtob     = atob(localStorage.getItem('senli'));        //
    let movipInput    = document.querySelector('input#movip');      //Campo de percentual de desconto

    //Anula desconto e oculta container da senha de autorização ao clicar no botão cancelar
    btnCancelPass.addEventListener('click', () =>
    {
        passwordGroup.classList.remove('active');
        movipInput.value = '';
        passwordInput.value = '';

        //Dispara evento para atualizar localStorage; veja:(fieldsAll.forEach((element)) e oculta container da senha (efeito cascata)
        movipInput.dispatchEvent(new Event('change'));
    });

    //Verificando senha e dando retorno ao usuário
    btnOKPassword.addEventListener('click', () => 
    {
        if(passwordInput.value == senliAtob)
        {
            passwordInput.value = '';
            passwordGroup.classList.remove('active');
            displayMessage('Desconto autorizado.', 'success'); //Mensagem de retorno
        }
        else
        {
            //Anima campo de senha -> rotate()
            let passwordField = document.querySelector('.password-group .field-group');
                passwordField.classList.add('anime-rotate');
                setTimeout(() => passwordField.classList.remove('anime-rotate'), 300);
            
            //Mensagem de retorno
            displayMessage('Senha incompatível.', 'alert'); 
        }
    });

    /**
     * Calculando entrada e o restante financiado
     */
    let fentrInput = document.querySelector('.fentr input'); //Valor da entrada
    let ftotaInput = document.querySelector('.ftota input'); //Restante

    fentrInput.addEventListener('input', () =>
    {
        //Valor com desconto
        let movdeInput = document.querySelector('.checkout .movde input');
        
        //Valor com desconto está em branco, usa valor sem desconto (movde fora do checkout)
        if(movdeInput.value == '')  movdeInput = document.querySelector('.movde input');
        
        //Calculando restante; Total financiado
        if(fentrInput.value > 0)
        {
            ftotaInput.value = ((+movdeInput.value) - +fentrInput.value).toFixed(2);
        }
        else ftotaInput.value = ''; //Se valor da entrada (fentrInput) é igual a 0 ou está em branco
        ftotaInput.dispatchEvent(new Event('change'));

        //Limpando sob cascata campos no HTML e em localStorage quando valor da entrada é modificado
        fnpreInput.value = '';              //Qtd. de parcelas
        fcalcInput.value = '';              //Valor da parcela
        checkoutItems.venda1['fnpre'] = ''; 
        checkoutItems.venda1['fcalc'] = ''; 
    });

    /**
     * Calculando valor das parcelas e definindo quantidade de parcelas
     */
    let fnpreInput = document.querySelector('.fnpre input'); //Quantidade de parcelas
    let fcalcInput = document.querySelector('.fcalc input'); //Valor da parcela

    fnpreInput.addEventListener('input', () =>
    {
        //Se fnpreInput.value é vazio...
        if(ftotaInput.value == '') ftotaInput.value = document.querySelector('.checkout .movde input').value; //Movde com desconto
        if(ftotaInput.value == '') ftotaInput.value = document.querySelector('.movde input').value;           //Movde sem desconto

        fcalcInput.value = (+ftotaInput.value / +fnpreInput.value).toFixed(2);
        fcalcInput.dispatchEvent(new Event('input'));
    });

    /**
     * Quando é obrigatório informar o cliente
     */
    let movncSelect = document.querySelector('#movnc');
    let codclInput  = document.querySelector('#codcl');

    //2 formas de pagamento exigem registar o cliente: A prazo e Cartão Fidelidade
    movncSelect.addEventListener('change', () =>
    {
        if(movncSelect.value == 'A prazo' || movncSelect.value == 'Cartão Fidelidade')
        {
            codclInput.setAttribute('required', '');
            codclInput.setCustomValidity('É preciso localizar e selecionar um cliente');
        }
        else
        {
            codclInput.removeAttribute('required');
            codclInput.setCustomValidity('');
        }
    });
    
    //Caso já esteja campo o campo preenchido
    if(movncSelect.value == 'A prazo' || movncSelect.value == 'Cartão Fidelidade') codclInput.setAttribute('required', '');
    codclInput.addEventListener('invalid', () => codclInput.setCustomValidity('É preciso localizar e selecionar um cliente'));
    codclInput.addEventListener('input',   () => codclInput.setCustomValidity(''));
}