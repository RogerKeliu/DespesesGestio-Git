<?php
declare(strict_types=1);

/**
 * Comprovacions de sessió i rol per a pàgines protegides.
 */
class Autenticacio
{
    public static function sessioIniciada(): bool
    {
        return isset($_SESSION['id']);
    }

    public static function esAdmin(): bool
    {
        return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
    }

    public static function requerirLogin(): void
    {
        if (!self::sessioIniciada()) {
            header('Location: login.php');
            exit;
        }
    }

    /** Només administradors; la resta va a la pàgina de creació (o es pot canviar més endavant). */
    public static function requerirAdmin(): void
    {
        self::requerirLogin();
        if (!self::esAdmin()) {
            header('Location: crearmoviments.php?error=no_admin');
            exit;
        }
    }
}
