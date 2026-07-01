<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'iTECH') ?></title>
    <link rel="stylesheet" href="/Parcial1/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <div class="logo">
            <span class="logo-icon">⚙️</span>
            <span class="logo-text">i<strong>TECH</strong></span>
        </div>
        <nav class="site-nav">
            <a href="/Parcial1/">Formulario</a>
            <a href="/Parcial1/reporte.php">Reporte</a>
            <a href="/Parcial1/export.php">Exportar Excel</a>
        </nav>
    </div>
</header>

<main class="site-main">
