//MESSAGE HANDLER
let messageBox = document.querySelector('.message');
let btnClose = document.querySelector('.message .close');

if(messageBox) {
    btnClose.addEventListener('click', () => {
        messageBox.style.display = 'none';
    });
    
    setTimeout(() => {
        messageBox.style.left = '0';
        messageBox.style.opacity = '1';
    }, 500);

    if(!messageBox.classList.contains('alert')) {
        setTimeout(() => {
            messageBox.style.left = '-100px';
            messageBox.style.opacity = '0';
        }, 5000);
        
        setTimeout(() => {
            messageBox.style.display = 'none';
        }, 5300);
    }
}

/**
 * 
 */
function displayMessage(message, className)
{
    let msgContainer = document.querySelector('.message');
    if(msgContainer) msgContainer.parentElement.removeChild(msgContainer);
    
    //
    msgContainer = document.createElement('div');
    msgContainer.classList.add('message', className);

    let spanMessage = document.createElement('span');
        spanMessage.innerText = message;

    let btnClose = document.createElement('button');
        btnClose.classList.add('close');
        btnClose.innerText = 'x';
        btnClose.addEventListener('click', () => msgContainer.classList.remove('active'));

    //
    msgContainer.appendChild(spanMessage);
    msgContainer.appendChild(btnClose);
    
    document.querySelector('body').appendChild(msgContainer);

    setTimeout(() => msgContainer.classList.add('active'), 350);
    if(className !== 'alert') setTimeout(() => msgContainer.classList.remove('active'), 5300);
}
window.displayMessage = displayMessage;