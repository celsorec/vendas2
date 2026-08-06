//COMPARTILHAR APP COM OUTROS COLABORADORES

const urlOriginal = window.location.pathname.split('/');
const urlSharing  = window.location.origin + '/' + urlOriginal[1];

new QRCode(document.getElementById("qrcode-url"), 
{
    text: urlSharing,
    width: 1080,
    height: 1080
});