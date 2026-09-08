//VERIFICAÇÃO E NOTIFICAÇÃO SOBRE VERSÕES

//Comparando números da versão
async function verification()
{
    //Arquivo JSON
    const jsonFile   = await fetch('./././settings.json', {cache: 'no-store'}); //Arquivo de configurações
    const jsonData   = await jsonFile.json();
    let   appVersion = localStorage.getItem('app_version');

    //Modal de alerta de atualização
    let modalNotify = document.querySelector('#update-notify');
    let spanVersion = modalNotify.querySelector('.modal span');
    spanVersion.innerText = jsonData.version; //Número da versão na modal

    //Se não existe versão em localStorage(app_version), armazena versão atual e atualiza variável
    if(!appVersion)
    {
        localStorage.setItem('app_version', jsonData.version);
        appVersion = localStorage.getItem('app_version');
    }

    //Se versão atual é diferente da nova versão, ativa modal de alerta de atualização
    if(appVersion !== jsonData.version) modalNotify.classList.add('active');

    //Obtendo versão do sistema na página Sobre
    let versionControl = document.querySelectorAll('#about h2');

    //Se carregou a págia Sobre para ver detalhes da atualização
    if(versionControl.length)
    {
        //Atualizando no aparelho do usuário o número da versão
        localStorage.setItem('app_version', jsonData.version);

        //Ocultando modal de alerta de atualização
        modalNotify.classList.remove('active');
    }
}

//Impedindo que verificação de versão ocorra na tela de login
const configView = document.querySelector('#config');
if(!configView) verification();