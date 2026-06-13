// MESSAGE HANDLER
let containerLoad = document.querySelector('#container-load');
let form  = document.querySelector('form');
let load  = document.querySelectorAll('.load'); //Links (a/button) que ativam animação load
let sbmit = document.querySelector('form button[type=submit]');

//Adiciona delay ao clicar em links (a/button) para ativar animação load como retorno ao usuário
if(load.length) {
    load.forEach((element) => {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            containerLoad.classList.add('active');
            const destination = this.getAttribute('href');
            setTimeout(() => {
                window.location.href = destination;
            }, 500);
        });
    });
}

//Adiciona delay ao submeter formulários para ativar animação load como retorno ao usuário
if(sbmit) {
    sbmit.addEventListener('click', function(e) {
        if (form.checkValidity()) {
            e.preventDefault();
            containerLoad.classList.add('active');
            setTimeout(() => {
                form.submit();
            }, 100);
        }
    });
}

//Oculta o load ao voltar com o navegador
window.addEventListener('pageshow', function() {
    containerLoad.classList.remove('active');
});