<?php
declare(strict_types=1);

/**
 * Plantilla: tancament del contingut principal, peu de pàgina i tancament del document.
 * Després d'incloure només cal tancar el PHP de la pàgina (si escau).
 */
?>
</main>
<footer class="border-top bg-white mt-auto py-3">
    <div class="container-fluid text-center text-muted small">
        <span>&copy; <?= htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8') ?> Gestió de despeses</span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
