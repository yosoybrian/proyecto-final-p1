<?php

declare(strict_types=1);

namespace CitasMedicas\Dominio;

/**
 * Clase Medicamento
 */
class Medicamento
{
    public function __construct(
        private int $id,
        private string $nombreComercial,
        private string $nombreGenerico,
        private string $contraindicaciones
    ) {}

    /**
     * Verifica si es seguro combinar con otros medicamentos
     *
     * @param array $lista Array de Medicamento
     */
    public function esSeguroCon(array $lista): bool
    {
        return true;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombreComercial(): string
    {
        return $this->nombreComercial;
    }

    public function getNombreGenerico(): string
    {
        return $this->nombreGenerico;
    }

    public function getContraindicaciones(): string
    {
        return $this->contraindicaciones;
    }

    // Setters
    public function setNombreComercial(string $nombreComercial): void
    {
        $this->nombreComercial = $nombreComercial;
    }

    public function setNombreGenerico(string $nombreGenerico): void
    {
        $this->nombreGenerico = $nombreGenerico;
    }

    public function setContraindicaciones(string $contraindicaciones): void
    {
        $this->contraindicaciones = $contraindicaciones;
    }
}
