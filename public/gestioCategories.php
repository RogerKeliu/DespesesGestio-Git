<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/objectes/Autenticacio.php';
require_once __DIR__ . '/objectes/Categoria.php';

Autenticacio::requerirAdmin();

function redirigirAmbMissatge(string $missatge): never
{
    header('Location: gestioCategories.php?msg=' . rawurlencode($missatge));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['alta'])) {
        $nom = trim((string) ($_POST['nom'] ?? ''));
        if ($nom === '') {
            redirigirAmbMissatge('nom_buit');
        }
        try {
            $categoria = new Categoria();
            $categoria->nom = $nom;
            $categoria->registrar();
            redirigirAmbMissatge('alta_ok');
        } catch (PDOException) {
            redirigirAmbMissatge('nom_duplicat');
        }
    } elseif (isset($_POST['desar'])) {
        $id = (int) ($_POST['id'] ?? 0);
        $nom = trim((string) ($_POST['nom'] ?? ''));
        if ($id < 1 || $nom === '') {
            redirigirAmbMissatge('desar_error');
        }
        try {
            $categoria = new Categoria();
            $categoria->id = $id;
            $categoria->nom = $nom;
            $categoria->actualitzar();
            redirigirAmbMissatge('desar_ok');
        } catch (PDOException) {
            redirigirAmbMissatge('nom_duplicat');
        }
    }
}

if (isset($_GET['accio'], $_GET['id']) && $_GET['accio'] === 'eliminar') {
    $idElim = (int) $_GET['id'];
    if ($idElim < 1) {
        redirigirAmbMissatge('eliminar_error');
    }
    try {
        $categoria = new Categoria();
        $categoria->id = $idElim;
        $categoria->eliminar();
        redirigirAmbMissatge('eliminar_ok');
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if (str_contains($msg, '1451') || str_contains($msg, 'Integrity constraint')) {
            redirigirAmbMissatge('eliminar_fk');
        }
        redirigirAmbMissatge('eliminar_error');
    }
}

$titolPagina = 'Gestió de categories — Gestió de despeses';
$plantillaSeccio = 'categories';
require __DIR__ . '/inclou/header.php';

$missatgeClau = isset($_GET['msg']) ? (string) $_GET['msg'] : null;
$textosMissatge = [
    'alta_ok' => ['success', "La categoria s'ha creat correctament."],
    'desar_ok' => ['success', "La categoria s'ha actualitzat correctament."],
    'eliminar_ok' => ['success', "La categoria s'ha eliminat correctament."],
    'nom_buit' => ['danger', 'El nom no pot estar buit.'],
    'nom_duplicat' => ['warning', 'Ja existeix una categoria amb aquest nom.'],
    'desar_error' => ['danger', "No s'ha pogut desar: dades incorrectes."],
    'eliminar_fk' => ['warning', "No es pot eliminar: hi ha moviments que fan servir aquesta categoria."],
    'eliminar_error' => ['danger', "No s'ha pogut eliminar la categoria."],
];

$accio = isset($_GET['accio']) ? (string) $_GET['accio'] : '';
$idGet = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$modeEdicio = $accio === 'editar' && $idGet > 0;

$categoriaEdicio = new Categoria();
if ($modeEdicio) {
    $categoriaEdicio->carregarPerId($idGet);
    if ($categoriaEdicio->id === null) {
        $modeEdicio = false;
        $categoriaNoTrobada = true;
    }
}

$categoriaLlistat = new Categoria();
$categories = $categoriaLlistat->llistarCategories();
?>

<div class="container py-4">
    <?php if ($missatgeClau !== null && isset($textosMissatge[$missatgeClau])): ?>
        <?php
        [$tipus, $text] = $textosMissatge[$missatgeClau];
        ?>
        <div class="alert alert-<?= htmlspecialchars($tipus, ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($text, ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tancar"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($categoriaNoTrobada) && $categoriaNoTrobada): ?>
        <div class="alert alert-warning" role="alert">No s'ha trobat la categoria sol·licitada.</div>
    <?php endif; ?>

    <?php if ($modeEdicio): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <h1 class="h3 mb-4 text-center">Editar categoria</h1>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="post" action="gestioCategories.php">
                            <input type="hidden" name="id" value="<?= (int) $categoriaEdicio->id ?>">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" id="nom" name="nom" class="form-control" required
                                       maxlength="255"
                                       value="<?= htmlspecialchars((string) $categoriaEdicio->nom, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <button type="submit" name="desar" class="btn btn-success">Desar</button>
                            <a href="gestioCategories.php" class="btn btn-outline-secondary ms-2">Cancel·lar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <h1 class="h3 mb-4 text-center">Crear categoria</h1>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="post" action="gestioCategories.php">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" id="nom" name="nom" class="form-control" required maxlength="255">
                            </div>
                            <button type="submit" name="alta" class="btn btn-primary">Alta</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <h2 class="h4 mb-3 text-center">Llistat de categories</h2>
            <div class="table-responsive card shadow-sm">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Acció</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categories as $fila): ?>
                        <tr>
                            <td><?= (int) $fila['id'] ?></td>
                            <td><?= htmlspecialchars((string) $fila['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="gestioCategories.php?id=<?= (int) $fila['id'] ?>&accio=editar"
                                   class="btn btn-warning btn-sm">Editar</a>
                                <a href="gestioCategories.php?id=<?= (int) $fila['id'] ?>&accio=eliminar"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Segur que vols eliminar aquesta categoria?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/inclou/footer.php'; ?>
