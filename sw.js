// PWA 

const CACHE_NAME = 'pwa-cache-v1';
const ASSETS = [
	'./',                  		  // Refere-se à pasta atual (vendas2/)
	'index.php',           		  // Se o seu arquivo principal for PHP
	'assets/css/style.css',       // Sem a barra '/' no início
	'assets/js/script.js'
];

// Instalação do Service Worker e armazenamento em cache
self.addEventListener('install', (event) => {
	event.waitUntil(
		caches.open(CACHE_NAME).then((cache) => {
			return cache.addAll(ASSETS);
		})
	);
});

// Interceptação de requisições para servir o conteúdo do cache offline
self.addEventListener('fetch', (event) => {
	event.respondWith(
		caches.match(event.request).then((response) => {
			return response || fetch(event.request);
		})
	);
});