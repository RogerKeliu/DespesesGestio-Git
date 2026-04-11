<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/objectes/Autenticacio.php';

Autenticacio::requerirLogin();

if (isset($_GET['error']) && $_GET['error'] === 'no_admin') {
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear moviment — Gestió de Despeses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <span class="navbar-brand">Gestió de Despeses</span>
        <div class="navbar-nav ms-auto flex-row gap-3 align-items-center">
            <span class="nav-link mb-0">Benvingut, <?= htmlspecialchars((string) $_SESSION['nom'], ENT_QUOTES, 'UTF-8') ?></span>
            <a class="nav-link" href="logout.php">Sortir</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h1 class="h3 mb-4">Crear moviment</h1>

            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger" role="alert">No tens permisos per accedir a la pagina GestioMoviments.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
