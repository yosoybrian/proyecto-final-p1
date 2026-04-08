<?php

declare(strict_types=1);

namespace CitasMedicas\Dominio;

use DateTime;

/**
 * Clase Diagnostico
 */
class Diagnostico
{
    public function __construct(
        private int $id,
        private Cita $cita,
        private string $descripcion,
        private string $sintomas,
        private DateTime $fecha,
        private string $tratamiento,
        private string $estadoClinico
    ) {}

    /**
     * Genera un informe del diagnóstico
     */
    public function generarInforme(): string
    {
        return "Informe del diagnóstico #{$this->id}";
    }

    /**
     * Cierra el diagnóstico
     */
    public function cerrar(): bool
    {
        return true;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getCita(): Cita
    {
        return $this->cita;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getSintomas(): string
    {
        return $this->sintomas;
    }

    public function getFecha(): DateTime
    {
        return $this->fecha;
    }

    public function getTratamiento(): string
    {
        return $this->tratamiento;
    }

    public function getEstadoClinico(): string
    {
        return $this->estadoClinico;
    }

    // Setters
    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function setTratamiento(string $tratamiento): void
    {
        $this->tratamiento = $tratamiento;
    }

    public function setEstadoClinico(string $estadoClinico): void
    {
        $this->estadoClinico = $estadoClinico;
    }
}
