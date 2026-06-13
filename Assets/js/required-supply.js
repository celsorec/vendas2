//REQUIRED SUPPLY and PRICE
let statu = document.querySelector('#statu'); //Status do pedido de compra
let codfo = document.querySelector('#codfo'); //Fornecedor
let preco = document.querySelector('#preco'); //Preço do fornecedor
let files = document.querySelector('#files'); //Arquivo do contrato em PDF

if(codfo && preco)
{
    statu.addEventListener('change', () =>
    {
        //Se valor do campo Status for igual a 'Pedido de Compra (PED)' campos 'codfo', 'preco' e 'files' passam a ser requeridos
        if(statu.value === 'PED')
        {
            codfo.setAttribute('required', '');
            preco.setAttribute('required', '');
            files.setAttribute('required', '');
        }
        else
        {
            codfo.removeAttribute('required');
            preco.removeAttribute('required');
            files.removeAttribute('required');
        }
    });   
}