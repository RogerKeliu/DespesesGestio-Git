<?php
declare(strict_types=1);

/**
 * Plantilla: inici del document HTML + barra de navegació.
 * Abans d'incloure: session_start() i comprovacions d'Autenticacio.
 *
 * @var string $titolPagina Text del <title>
 * @var string|null $plantillaSeccio 'moviments' | 'categories' — pestanya activa (només admin)
 */

$titolPagina = $titolPagina ?? 'Gestió de despeses';

require_once dirname(__DIR__) . '/objectes/Autenticacio.php';

$plantillaSeccio = $plantillaSeccio ?? null;
$nomUsuari = isset($_SESSION['nom']) ? (string) $_SESSION['nom'] : '';
$plantillaEsAdmin = Autenticacio::esAdmin();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titolPagina, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
<nav class="navbar navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <div class="row align-items-center w-100 g-2 py-1">
            <div class="col-12 col-md-4 text-center text-md-start">
                <span class="navbar-brand mb-0">Gestió de despeses</span>
            </div>
            <div class="col-12 col-md-4 text-center">
                <span class="nav-link py-1 mb-0 d-inline-block">Benvingut, <?= htmlspecialchars($nomUsuari, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="col-12 col-md-4">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-end gap-2">
                    <?php if ($plantillaEsAdmin): ?>
                        <a class="nav-link p-2 <?= $plantillaSeccio === 'moviments' ? 'fw-semibold text-dark' : '' ?>"
                           href="gestiomoviments.php">Moviments</a>
                        <a class="nav-link p-2 <?= $plantillaSeccio === 'categories' ? 'fw-semibold text-dark' : '' ?>"
                           href="gestioCategories.php">Categories</a>
                    <?php endif; ?>
                    <a class="nav-link p-2 <?= $plantillaEsAdmin ? 'ms-3 ms-md-4' : '' ?>"
                       href="logout.php">Sortir</a>
                </div>
            </div>
        </div>
    </div>
</nav>
<main class="flex-grow-1">
