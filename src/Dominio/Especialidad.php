<?php

declare(strict_types=1);

namespace CitasMedicas\Dominio;

/**
 * Clase Especialidad
 */
class Especialidad
{
    public function __construct(
        private int $id,
        private string $nombre
    ) {}

    /**
     * Valida la disponibilidad de la especialidad
     */
    public function validarDisponibilidad(): bool
    {
        return true;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    // Setters
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }
}
