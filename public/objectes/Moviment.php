<?php
declare(strict_types=1);

require_once __DIR__ . '/Connexio.php';

/**
 * Accés a la taula moviments (id_usuari, id_categoria, concepte, import, data).
 */
class Moviment extends Connexio
{
    public ?int $id = null;
    public ?int $idUsuari = null;
    public ?int $idCategoria = null;
    public ?string $concepte = null;
    /** Import com a cadena amb format decimal (p. ex. "12.50"). */
    public ?string $import = null;
    /** Data en format Y-m-d. */
    public ?string $data = null;

    public function carregarPerId(int $id): void
    {
        $stmt = $this->connectar()->prepare(
            'SELECT id, id_usuari, id_categoria, concepte, import, data FROM moviments WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) {
            $this->id = (int) $row['id'];
            $this->idUsuari = (int) $row['id_usuari'];
            $this->idCategoria = (int) $row['id_categoria'];
            $this->concepte = (string) $row['concepte'];
            $this->import = (string) $row['import'];
            $this->data = (string) $row['data'];
        } else {
            $this->id = null;
            $this->idUsuari = null;
            $this->idCategoria = null;
            $this->concepte = null;
            $this->import = null;
            $this->data = null;
        }
    }

    /**
     * @return list<array{
     *     id: int,
     *     id_usuari: int,
     *     id_categoria: int,
     *     concepte: string,
     *     import: string,
     *     data: string,
     *     usuari_nom: string
     * }>
     */
    public function llistarMoviments(): array
    {
        $sql = 'SELECT m.id, m.id_usuari, m.id_categoria, m.concepte, m.import, m.data,
                       u.nom AS usuari_nom
                FROM moviments m
                INNER JOIN usuaris u ON u.id = m.id_usuari
                ORDER BY m.data DESC, m.id DESC';
        $stmt = $this->connectar()->query($sql);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(static function (array $r): array {
            return [
                'id' => (int) $r['id'],
                'id_usuari' => (int) $r['id_usuari'],
                'id_categoria' => (int) $r['id_categoria'],
                'concepte' => (string) $r['concepte'],
                'import' => (string) $r['import'],
                'data' => (string) $r['data'],
                'usuari_nom' => (string) $r['usuari_nom'],
            ];
        }, $rows);
    }

    public function registrar(): void
    {
        if ($this->idUsuari === null || $this->idUsuari < 1) {
            throw new InvalidArgumentException('Usuari obligatori.');
        }
        if ($this->idCategoria === null || $this->idCategoria < 1) {
            throw new InvalidArgumentException('Categoria obligatòria.');
        }
        $concepte = $this->concepte !== null ? trim($this->concepte) : '';
        if ($concepte === '') {
            throw new InvalidArgumentException('El concepte és obligatori.');
        }
        if ($this->import === null || $this->import === '' || !is_numeric($this->import)) {
            throw new InvalidArgumentException('Import no vàlid.');
        }
        if ($this->data === null || $this->data === '') {
            throw new InvalidArgumentException('Data obligatòria.');
        }

        $pdo = $this->connectar();
        $stmt = $pdo->prepare(
            'INSERT INTO moviments (id_usuari, id_categoria, concepte, import, data)
             VALUES (:id_usuari, :id_categoria, :concepte, :import, :data)'
        );
        $stmt->execute([
            ':id_usuari' => $this->idUsuari,
            ':id_categoria' => $this->idCategoria,
            ':concepte' => $concepte,
            ':import' => $this->import,
            ':data' => $this->data,
        ]);
        $this->id = (int) $pdo->lastInsertId();
    }

    public function actualitzar(): void
    {
        if ($this->id === null || $this->id < 1) {
            throw new InvalidArgumentException('Id obligatori.');
        }
        if ($this->idCategoria === null || $this->idCategoria < 1) {
            throw new InvalidArgumentException('Categoria obligatòria.');
        }
        $concepte = $this->concepte !== null ? trim($this->concepte) : '';
        if ($concepte === '') {
            throw new InvalidArgumentException('El concepte és obligatori.');
        }
        if ($this->import === null || $this->import === '' || !is_numeric($this->import)) {
            throw new InvalidArgumentException('Import no vàlid.');
        }
        if ($this->data === null || $this->data === '') {
            throw new InvalidArgumentException('Data obligatòria.');
        }

        $stmt = $this->connectar()->prepare(
            'UPDATE moviments SET id_categoria = :id_categoria, concepte = :concepte, import = :import, data = :data
             WHERE id = :id'
        );
        $stmt->execute([
            ':id_categoria' => $this->idCategoria,
            ':concepte' => $concepte,
            ':import' => $this->import,
            ':data' => $this->data,
            ':id' => $this->id,
        ]);
    }

    public function eliminar(): void
    {
        if ($this->id === null || $this->id < 1) {
            throw new InvalidArgumentException('Id obligatori per eliminar.');
        }
        $stmt = $this->connectar()->prepare('DELETE FROM moviments WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }
}
