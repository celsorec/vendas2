/* Ajusta largura dos campos subtt[] (Subtotal) para quantidade de caracteres inseridos  */

let subtt = document.querySelector('.subtt input');
let movde = document.querySelector('.movde input');

if(subtt || movde)
{
    function resize(element)
    {
        element.style.width = (element.value.length) + 'ch';
        element.style.maxWidth = '10ch'; //Até um 1 milhão
    }

    subtt.addEventListener('input',  () => resize(subtt));
    subtt.addEventListener('change', () => resize(subtt));

    movde.addEventListener('input',  () => resize(movde));
    movde.addEventListener('change', () => resize(movde));
}