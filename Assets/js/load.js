// MESSAGE HANDLER
let containerLoad = document.querySelector('#container-load');
let form = document.querySelector('form');
let links = document.querySelectorAll('a');
let sbmit = document.querySelector('form #sbmit');

//Adiciona delay ao clicar em links
if(links.length) {
    links.forEach((element) => {
        if(!element.classList.contains('linknull'))
        {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                containerLoad.classList.add('active');
                const destino = this.getAttribute('href');
                setTimeout(() => {
                    window.location.href = destino;
                }, 500);
            });
        }
    });
}

//Adiciona delay ao submeter formulários
if(sbmit) {
    sbmit.addEventListener('click', function(e) {
        if (form.checkValidity()) {
            e.preventDefault();
            containerLoad.classList.add('active');
            setTimeout(() => {
                form.submit();
            }, 500);
        }
    });
}

//Oculta o load ao voltar com o navegador
window.addEventListener('pageshow', function() {
    containerLoad.classList.remove('active');
});