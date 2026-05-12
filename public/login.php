<?php
session_start();

require_once __DIR__ . '/objectes/Usuari.php';

// Un cop loguejats, evitar poder entrar al login sense un logout primer (Evitar doble login)
if (isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

// Verificar si s'ha enviat el formulari de login
if (isset($_POST['nom']) && isset($_POST['contrasenya'])) {
    $usuari = new Usuari($_POST['nom'], $_POST['contrasenya']);

    if ($usuari->login()) {
        $_SESSION['id'] = $usuari->getId();
        $_SESSION['nom'] = $usuari->getNom();
        $_SESSION['rol'] = $usuari->getRol();
        
        // Sense exit(), el header nomes afageix la capçalera HTTP i continua la execució del script
            // El client pot rebre redirecció + cos HTML en la mateixa resposta, cosa innecessària i poc clara.

        header('Location: index.php');
        exit();
    } else {
        $loginError = true;
    }
}
?>

<!DOCTYPE html
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió de Despeses - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-body">
                    <h3 class="text-center">Iniciar Sessió</h3>
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom d'Usuari</label>
                            <input type="text" class="form-control" name="nom" id="nom" placeholder="Introdueix el teu nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasenya" class="form-label">Contrasenya</label>
                            <input type="password" class="form-control" name="contrasenya" id="contrasenya" placeholder="Introdueix la teva contrasenya" required>
                        </div>
                        <?php
                        // Si el login falla, mostrem un missatge d'error
                        if (isset($loginError) && $loginError) {
                            echo '<div class="alert alert-danger">Usuari o contrasenya incorrectes</div>';
                        }
                        ?>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Iniciar Sessió</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
