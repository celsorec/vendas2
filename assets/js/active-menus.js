//ACTIVE MENUS
const itenMenu = document.querySelectorAll('.menu > li a');
const plusMenu = document.querySelector('.plus-menu');
const submenu  = document.querySelector('.submenu');

if(itenMenu.length)
{
    //Ativando / desativando itens do menu principal
    itenMenu.forEach((element) =>
    {
        element.addEventListener('click', () =>
        {
            itenMenu.forEach((e) =>
            {
                e.classList.remove('active');
            });
    
            element.classList.add('active');
        });
    });
    
    //Ativando / desativando submenu
    plusMenu.addEventListener('click', () =>
    {
        submenu.classList.toggle('active');
    });
    
    //Fechar submenu ao clicar fora
    document.addEventListener('click', (e) =>
    {
        if(!submenu.contains(e.target) && !plusMenu.contains(e.target))
        {
            submenu.classList.remove('active');
            plusMenu.classList.remove('active');
        }
    });
}