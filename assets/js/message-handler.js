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