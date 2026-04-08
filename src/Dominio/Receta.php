<?php

declare(strict_types=1);

namespace CitasMedicas\Dominio;

use DateTime;

/**
 * Clase Receta
 */
class Receta
{
    public function __construct(
        private int $id,
        private Diagnostico $diagnostico,
        private array $medicamentos,
        private DateTime $fecha,
        private int $vigenciaDias
    ) {}

    /**
     * Genera un PDF de la receta
     */
    public function generarPDF(): string
    {
        return "PDF generado";
    }

    /**
     * Verifica si la receta está vigente
     */
    public function estaVigente(): bool
    {
        $fechaActual = new DateTime();
        $diasTranscurridos = $fechaActual->diff($this->fecha)->days;
        return $diasTranscurridos <= $this->vigenciaDias;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getDiagnostico(): Diagnostico
    {
        return $this->diagnostico;
    }

    public function getMedicamentos(): array
    {
        return $this->medicamentos;
    }

    public function getFecha(): DateTime
    {
        return $this->fecha;
    }

    public function getVigenciaDias(): int
    {
        return $this->vigenciaDias;
    }

    // Setters
    public function setVigenciaDias(int $vigenciaDias): void
    {
        $this->vigenciaDias = $vigenciaDias;
    }
}
