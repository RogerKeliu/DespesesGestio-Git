<?php
require_once __DIR__ . '/Connexio.php';

class Usuari extends Connexio
{
    private ?int $id = null;
    private ?string $nom = null;
    private ?string $contrasenya = null;
    private ?string $rol = null;

    public function __construct(
        string $nom,
        string $contrasenya,
        ?string $rol = null,
    ) {
        $this->nom = $nom;
        $this->contrasenya = $contrasenya;
        $this->rol = $rol;
    }

    /**
     * Methods
     */
    public function login(): bool
    {
        $query = "SELECT id, nom, contrasenya, rol FROM usuaris WHERE nom = :nom";
        $stmt = $this->connectar()->prepare($query);
        $stmt->bindParam(":nom", $this->nom, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($this->contrasenya, $row['contrasenya'])) {
            $this->id = (int) $row['id'];
            $this->nom = $row['nom'];
            $this->rol = $row['rol'];

            // Borrarem la contrasenya de la propietat per seguretat (No accessible des del codi sense hash)
            $this->contrasenya = null;
            return true;
        }
        $this->contrasenya = null;
        return false;
    }

    // ------------------------------ // 

    /**
     * Getters 
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
    
    public function getRol(): string
    {
        return $this->rol;
    }
}
