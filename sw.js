//PWA 
//Força Service Worker a ativar imediatamente após a instalação, ignorando a fase de espera
self.addEventListener('install', (event) => {
    self.skipWaiting(); 
});

//Instrui o Service Worker recém-ativado a assumir controle imediato de todas as páginas do cliente
self.addEventListener('activate', (event) => {
    event.waitUntil(
        self.clients.claim()
    );
});

//Intercepta requisições e as envia servidor, sem utilizar cache local
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request)
    );
});