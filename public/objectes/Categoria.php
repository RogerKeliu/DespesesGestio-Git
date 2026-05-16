<?php
declare(strict_types=1);

require_once __DIR__ . '/Connexio.php';

/**
 * Accés a la taula categories (id, nom).
 */
class Categoria extends Connexio
{
    public ?int $id = null;
    public ?string $nom = null;

    public function carregarPerId(int $id): void
    {
        $stmt = $this->connectar()->prepare('SELECT id, nom FROM categories WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) {
            $this->id = (int) $row['id'];
            $this->nom = (string) $row['nom'];
        } else {
            $this->id = null;
            $this->nom = null;
        }
    }

    /** @return list<array{id: int, nom: string}> */
    public function llistarCategories(): array
    {
        $stmt = $this->connectar()->query('SELECT id, nom FROM categories ORDER BY nom ASC');
        /** @var list<array{id: int, nom: string}> */
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar(): void
    {
        if ($this->nom === null || $this->nom === '') {
            throw new InvalidArgumentException('El nom és obligatori.');
        }
        $pdo = $this->connectar();
        $stmt = $pdo->prepare('INSERT INTO categories (nom) VALUES (:nom)');
        $stmt->execute([':nom' => $this->nom]);
        $this->id = (int) $pdo->lastInsertId();
    }

    public function actualitzar(): void
    {
        if ($this->id === null || $this->nom === null || $this->nom === '') {
            throw new InvalidArgumentException('Id i nom són obligatoris.');
        }
        $stmt = $this->connectar()->prepare('UPDATE categories SET nom = :nom WHERE id = :id');
        $stmt->execute([
            ':nom' => $this->nom,
            ':id' => $this->id,
        ]);
    }

    public function eliminar(): void
    {
        if ($this->id === null) {
            throw new InvalidArgumentException('Id obligatori per eliminar.');
        }
        $stmt = $this->connectar()->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }
}
