//DELETE CONFIRM
let btnDelete = document.querySelector('.btn-delete');
if(btnDelete)
{
    btnDelete.addEventListener('click', function(e)
    {
        e.preventDefault();
        e.stopImmediatePropagation();

        if(confirm('Quer realmente excluir?'))
        {
            window.location.href = this.href;
        }
    }, true);
}