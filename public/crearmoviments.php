<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/objectes/Autenticacio.php';
require_once __DIR__ . '/objectes/Categoria.php';
require_once __DIR__ . '/objectes/Moviment.php';

Autenticacio::requerirLogin();

function redirigirAmbMissatge(string $missatge): never
{
    header('Location: crearmoviments.php?msg=' . rawurlencode($missatge));
    exit;
}

$idSessio = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alta'])) {
    if ($idSessio < 1) {
        redirigirAmbMissatge('sessio_error');
    }
    $concepte = trim((string) ($_POST['concepte'] ?? ''));
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
    $data = trim((string) ($_POST['data'] ?? ''));
    $importRaw = trim((string) ($_POST['import'] ?? ''));

    if ($concepte === '' || $idCategoria < 1 || $data === '' || $importRaw === '' || !is_numeric($importRaw)) {
        redirigirAmbMissatge('dades_error');
    }

    try {
        $moviment = new Moviment();
        $moviment->idUsuari = $idSessio;
        $moviment->idCategoria = $idCategoria;
        $moviment->concepte = $concepte;
        $moviment->import = $importRaw;
        $moviment->data = $data;
        $moviment->registrar();
        redirigirAmbMissatge('alta_ok');
    } catch (InvalidArgumentException) {
        redirigirAmbMissatge('dades_error');
    } catch (PDOException) {
        redirigirAmbMissatge('dades_error');
    }
}

if (isset($_GET['error']) && $_GET['error'] === 'no_admin') {
    $error = true;
}

$titolPagina = 'Crear moviment — Gestió de despeses';
$plantillaSeccio = null;
require __DIR__ . '/inclou/header.php';

$missatgeClau = isset($_GET['msg']) ? (string) $_GET['msg'] : null;
$textosMissatge = [
    'alta_ok' => ['success', "El moviment s'ha creat correctament."],
    'dades_error' => ['danger', "Revisa el concepte, la categoria, la data i l'import."],
    'sessio_error' => ['danger', 'Sessió no vàlida. Torna a iniciar sessió.'],
];

$categoriaModel = new Categoria();
$categories = $categoriaModel->llistarCategories();
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-4 text-center">Crear moviment</h1>

            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger" role="alert">No tens permisos per accedir a la pàgina de gestió de moviments.</div>
            <?php endif; ?>

            <?php if ($missatgeClau !== null && isset($textosMissatge[$missatgeClau])): ?>
                <?php
                [$tipus, $text] = $textosMissatge[$missatgeClau];
                ?>
                <div class="alert alert-<?= htmlspecialchars($tipus, ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($text, ENT_QUOTES, 'UTF-8') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tancar"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="post" action="crearmoviments.php">
                        <div class="mb-3">
                            <label for="concepte" class="form-label">Concepte</label>
                            <input type="text" id="concepte" name="concepte" class="form-control" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="id_categoria" class="form-label">Categoria</label>
                            <select id="id_categoria" name="id_categoria" class="form-select" required>
                                <option value="">Selecciona una categoria</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int) $cat['id'] ?>">
                                        <?= htmlspecialchars((string) $cat['nom'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" id="data" name="data" class="form-control" required
                                   value="<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="import" class="form-label">Import</label>
                            <input type="number" id="import" name="import" class="form-control" step="0.01" required>
                        </div>
                        <button type="submit" name="alta" class="btn btn-primary">Alta</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/inclou/footer.php'; ?>
