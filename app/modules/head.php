<?php
//Obtendo localização da loja através do domínio
$location = explode(':', $_SERVER['HTTP_HOST'])[0];
$location = explode('.', $location);

$baseUrl     = 'https://'.$location[0].'.gcontrol.site/vendas';
$appName     = 'App Vendas';
$description = 'Uma extensão do GControl Desktop';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="icon" type="image/svg" href="assets/images/favicon.svg">

    <!-- ASSETS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>

    <!-- FONTS GOOGLE -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- PWA -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#007BFF">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- BIBLIOTECAS EXTERNAS -->
    <script src="https://unpkg.com/@zxing/library@latest"></script> <!-- Câmera iPhone -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script> <!-- Share App -->

    <!-- OPEN GRAPH -->
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?= $appName; ?>" />
	<meta property="og:description" content="<?= $description; ?>" />
	<meta property="og:locale" content="pt_BR">
	<meta property="og:site_name" content="<?= $appName; ?>" />
	<meta property="og:image" content="<?= $baseUrl; ?>/assets/images/og_image.png" />
	<meta property="og:image:type" content="image/png" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:type" content="image/jpg" />
	<meta property="og:image:width" content="1200">
	<meta property="og:url" content="<?= $baseUrl; ?>">
	<meta name="robots" content="max-image-preview:large">
</head>
<body>