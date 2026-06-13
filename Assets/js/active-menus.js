//ACTIVE MENUS
let linkmenu = document.querySelectorAll('nav ul li a');

linkmenu.forEach((element) => {
    element.addEventListener('click', () => {        
        linkmenu.forEach((element) => {
            element.parentElement.classList.remove('active');
            element.parentElement.style.height = null;
        });

        //Salvando em localStorage item ativo para destacar ao mudar de página
        if(element.parentElement.className == 'item')
        {
            localStorage.setItem('activeItem', element.parentElement.id);
        }
        
        element.parentElement.classList.add('active');
        element.parentElement.style.height = element.parentElement.scrollHeight + 'px';
    });
});

//Buscando em localStorage item ativo para destacar ao mudar de página
let activeItem = document.querySelector('#'+localStorage.getItem('activeItem'));
if(activeItem)
{
    activeItem.classList.add('active');
    activeItem.style.height = activeItem.scrollHeight + 'px';
}

//Expansão e contração do menu geral
let btnmenu = document.querySelector('.btnmenu');
btnmenu.addEventListener('click', () => {
    let mainmenu = document.querySelector('#mainmenu');
    let main = document.querySelector('#main');
    mainmenu.classList.toggle('hidden');
    main.classList.toggle('full-width');
});