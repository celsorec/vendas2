//CALC BALANCE - Compara valor do produto * quantidade com saldo orçamentário disponível
const codit = document.querySelector("#codit"); //Campo códido item
const tmprc = document.querySelector("#tmprc"); //Campo preço do item
const qtdto = document.querySelector("#qtdto"); //Quantidade desejada para compra
const dispo = document.querySelector("#dispo"); //Saldo disponível
const ttcpr = document.querySelector("#ttcpr"); //Valor Total da Compra
const sbmit = document.querySelector('#sbmit'); //Botões disponíveis na página
const btntx = sbmit.querySelector('.text');     //Texto do botão

//Função de cálculo e de comparação
function calcBalance()
{
    if(tmprc.value && qtdto.value && dispo) //Itens necessários
    {
        setTimeout(() => { //setTimeout porque valor do campo tmprc(preço do item) não existe inicialmente
            if(tmprc.value * qtdto.value > dispo.innerText) //Se valor * quantidade MAIOR QUE saldo disponível
            {
                sbmit.setAttribute('disabled', '');
                sbmit.classList.add('alert');
                ttcpr.innerText = (tmprc.value * qtdto.value).toFixed(2);
                ttcpr.style.color = '#b30011';
                dispo.style.color = '#b30011';
                btntx.innerText = 'Saldo Insuficiente';
            }
            else
            {
                sbmit.removeAttribute('disabled');
                sbmit.classList.remove('alert');
                ttcpr.innerText = (tmprc.value * qtdto.value).toFixed(2);
                ttcpr.style.color = '#4d4d4dff';
                dispo.style.color = '#4d4d4dff';
                btntx.innerText = 'Salvar';
            }
        }, 200);
    }
}

//Campos que acionam a função de comparação
if(tmprc && qtdto && dispo)
{
    codit.addEventListener('change', () => {calcBalance();});
    tmprc.addEventListener('change', () => {calcBalance();});
    qtdto.addEventListener('change', () => {calcBalance();});
    calcBalance();
}