//SEARCH FIELDS
let fields = document.querySelectorAll('.search input');

if(fields.length)
{
    fields.forEach((element) => {
        if(element)
        {
            element.addEventListener('click', () => {
                element.value = null; //Limpar campo para evitar edição e envio de informações equivocadas
            });
        }
    });
}