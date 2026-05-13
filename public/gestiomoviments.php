<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/objectes/Autenticacio.php';
require_once __DIR__ . '/objectes/Categoria.php';
require_once __DIR__ . '/objectes/Moviment.php';

Autenticacio::requerirAdmin();

function redirigirAmbMissatge(string $missatge): never
{
    header('Location: gestiomoviments.php?msg=' . rawurlencode($missatge));
    exit;
}

$idSessio = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['alta'])) {
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
    } elseif (isset($_POST['desar'])) {
        $id = (int) ($_POST['id'] ?? 0);
        $concepte = trim((string) ($_POST['concepte'] ?? ''));
        $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
        $data = trim((string) ($_POST['data'] ?? ''));
        $importRaw = trim((string) ($_POST['import'] ?? ''));

        if ($id < 1 || $concepte === '' || $idCategoria < 1 || $data === '' || $importRaw === '' || !is_numeric($importRaw)) {
            redirigirAmbMissatge('desar_error');
        }

        try {
            $moviment = new Moviment();
            $moviment->carregarPerId($id);
            if ($moviment->id === null) {
                redirigirAmbMissatge('moviment_no_trobat');
            }
            $moviment->idCategoria = $idCategoria;
            $moviment->concepte = $concepte;
            $moviment->import = $importRaw;
            $moviment->data = $data;
            $moviment->actualitzar();
            redirigirAmbMissatge('desar_ok');
        } catch (InvalidArgumentException) {
            redirigirAmbMissatge('desar_error');
        } catch (PDOException) {
            redirigirAmbMissatge('desar_error');
        }
    }
}

if (isset($_GET['accio'], $_GET['id']) && $_GET['accio'] === 'eliminar') {
    $idElim = (int) $_GET['id'];
    if ($idElim < 1) {
        redirigirAmbMissatge('eliminar_error');
    }
    try {
        $moviment = new Moviment();
        $moviment->id = $idElim;
        $moviment->eliminar();
        redirigirAmbMissatge('eliminar_ok');
    } catch (PDOException) {
        redirigirAmbMissatge('eliminar_error');
    }
}

$titolPagina = 'Gestió de moviments — Gestió de despeses';
$plantillaSeccio = 'moviments';
require __DIR__ . '/inclou/header.php';

$missatgeClau = isset($_GET['msg']) ? (string) $_GET['msg'] : null;
$textosMissatge = [
    'alta_ok' => ['success', "El moviment s'ha creat correctament."],
    'desar_ok' => ['success', "El moviment s'ha actualitzat correctament."],
    'eliminar_ok' => ['success', "El moviment s'ha eliminat correctament."],
    'dades_error' => ['danger', "Revisa el concepte, la categoria, la data i l'import."],
    'desar_error' => ['danger', "No s'ha pogut desar el moviment."],
    'moviment_no_trobat' => ['warning', "No s'ha trobat el moviment."],
    'eliminar_error' => ['danger', "No s'ha pogut eliminar el moviment."],
    'sessio_error' => ['danger', 'Sessió no vàlida. Torna a iniciar sessió.'],
];

$accio = isset($_GET['accio']) ? (string) $_GET['accio'] : '';
$idGet = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$modeEdicio = $accio === 'editar' && $idGet > 0;

$movimentEdicio = new Moviment();
if ($modeEdicio) {
    $movimentEdicio->carregarPerId($idGet);
    if ($movimentEdicio->id === null) {
        $modeEdicio = false;
        $movimentNoTrobat = true;
    }
}

$categoriaModel = new Categoria();
$categories = $categoriaModel->llistarCategories();

$movimentLlistat = new Moviment();
$moviments = $movimentLlistat->llistarMoviments();
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

    <?php if (isset($movimentNoTrobat) && $movimentNoTrobat): ?>
        <div class="alert alert-warning" role="alert">No s'ha trobat el moviment sol·licitat.</div>
    <?php endif; ?>

    <?php if ($modeEdicio): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <h1 class="h3 mb-4 text-center">Editar moviment</h1>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="post" action="gestiomoviments.php">
                            <input type="hidden" name="id" value="<?= (int) $movimentEdicio->id ?>">
                            <div class="mb-3">
                                <label for="concepte" class="form-label">Concepte</label>
                                <input type="text" id="concepte" name="concepte" class="form-control" required maxlength="255"
                                       value="<?= htmlspecialchars((string) $movimentEdicio->concepte, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="id_categoria" class="form-label">Categoria</label>
                                <select id="id_categoria" name="id_categoria" class="form-select" required>
                                    <option value="">Selecciona una categoria</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= (int) $cat['id'] ?>"
                                            <?= (int) $cat['id'] === (int) $movimentEdicio->idCategoria ? ' selected' : '' ?>>
                                            <?= htmlspecialchars((string) $cat['nom'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="data" class="form-label">Data</label>
                                <input type="date" id="data" name="data" class="form-control" required
                                       value="<?= htmlspecialchars((string) $movimentEdicio->data, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="import" class="form-label">Import</label>
                                <input type="number" id="import" name="import" class="form-control" step="0.01" required
                                       value="<?= htmlspecialchars((string) $movimentEdicio->import, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <button type="submit" name="desar" class="btn btn-success">Desar</button>
                            <a href="gestiomoviments.php" class="btn btn-outline-secondary ms-2">Cancel·lar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <h1 class="h3 mb-4 text-center">Crear moviment</h1>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="post" action="gestiomoviments.php">
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
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <h2 class="h4 mb-3 text-center">Llistat de moviments</h2>
            <div class="table-responsive card shadow-sm">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Concepte</th>
                        <th scope="col">ID categoria</th>
                        <th scope="col">Usuari</th>
                        <th scope="col">Data</th>
                        <th scope="col" class="text-end">Import</th>
                        <th scope="col">Acció</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($moviments as $fila): ?>
                        <tr>
                            <td><?= (int) $fila['id'] ?></td>
                            <td><?= htmlspecialchars((string) $fila['concepte'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int) $fila['id_categoria'] ?></td>
                            <td><?= htmlspecialchars((string) $fila['usuari_nom'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $fila['data'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end"><?= htmlspecialchars((string) $fila['import'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="gestiomoviments.php?id=<?= (int) $fila['id'] ?>&accio=editar"
                                   class="btn btn-warning btn-sm">Editar</a>
                                <a href="gestiomoviments.php?id=<?= (int) $fila['id'] ?>&accio=eliminar"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Segur que vols eliminar aquest moviment?');">Eliminar</a>
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
