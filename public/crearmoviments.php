<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/objectes/Autenticacio.php';

Autenticacio::requerirLogin();

if (isset($_GET['error']) && $_GET['error'] === 'no_admin') {
    $error = true;
}

$titolPagina = 'Crear moviment — Gestió de despeses';
$plantillaSeccio = null;
require __DIR__ . '/inclou/header.php';
?>

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

<?php require __DIR__ . '/inclou/footer.php'; ?>
