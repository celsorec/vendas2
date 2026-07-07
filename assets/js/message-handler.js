//MESSAGE HANDLER

/**
 * Lida com mensagens vindas do PHP, via sessão
 */
let messageBox = document.querySelector('.message');
let btnClose = document.querySelector('.message .close');

if(messageBox) {
    //Oculta ao clique no botão fechar
    btnClose.addEventListener('click', () => {
        messageBox.style.left = '-100px';
        messageBox.style.opacity = '0';
        messageBox.style.visibility = 'hidden';
        
        //Desativa completamente boxMessage
        setTimeout(() => messageBox.style.display = 'none', 300);
    });
    
    //Ativando mensagem
    setTimeout(() => {
        messageBox.style.left = '0';
        messageBox.style.opacity = '1';
        messageBox.style.visibility = 'visible';
    }, 500);

    //Mensagem que não são da classe 'alert' não desaparecem automaticamente
    if(!messageBox.classList.contains('alert')) {
        setTimeout(() => {
            messageBox.style.left = '-100px';
            messageBox.style.opacity = '0';
            messageBox.style.visibility = 'hidden';
        }, 5000);
        
        //Desativa completamente boxMessage
        setTimeout(() => messageBox.style.display = 'none', 5300);
    }
}

/**
 * Lida com mensagens envidas diretamente pelo JS
 */
function displayMessage(message, className)
{
    let msgContainer = document.querySelector('.message');
    if(msgContainer) msgContainer.parentElement.removeChild(msgContainer); //Removendo mensagens já exibidas
    
    //Criando novo componente de mensagens
    msgContainer = document.createElement('div');
    msgContainer.classList.add('message', className);

    let spanMessage = document.createElement('span');
        spanMessage.innerText = message;

    let btnClose = document.createElement('button');
        btnClose.classList.add('close');
        btnClose.innerText = 'x';
        btnClose.addEventListener('click', () => msgContainer.classList.remove('active'));

    //Montando componente
    msgContainer.appendChild(spanMessage);
    msgContainer.appendChild(btnClose);
    
    //Anexando componente de mensagens ao body
    document.querySelector('body').appendChild(msgContainer);

    //Exibindo e ocultando mensagem
    setTimeout(() => msgContainer.classList.add('active'), 350);
    if(className !== 'alert') setTimeout(() => msgContainer.classList.remove('active'), 5300);
}
window.displayMessage = displayMessage;