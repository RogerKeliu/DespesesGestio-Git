<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/objectes/Autenticacio.php';

Autenticacio::requerirAdmin();

$titolPagina = 'Gestió de moviments — Gestió de despeses';
$plantillaSeccio = 'moviments';
require __DIR__ . '/inclou/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h1 class="h3 mb-4">Gestió de moviments</h1>

        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h2 class="h4 mb-3">Llistat de moviments</h2>

        </div>
    </div>
</div>

<?php require __DIR__ . '/inclou/footer.php'; ?>
