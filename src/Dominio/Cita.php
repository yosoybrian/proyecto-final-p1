<?php

declare(strict_types=1);

namespace CitasMedicas\Dominio;

use CitasMedicas\Usuarios\Paciente;
use CitasMedicas\Usuarios\Medico;
use DateTime;

/**
 * Clase Cita
 */
class Cita
{
    public function __construct(
        private int $idCita,
        private Paciente $paciente,
        private Medico $medico,
        private DateTime $fecha,
        private string $hora,
        private string $estado,
        private string $motivo
    ) {}

    /**
     * Cambia el estado de la cita
     */
    public function cambiarEstado(string $estado): bool
    {
        $this->estado = $estado;
        return true;
    }

    /**
     * Verifica si la cita es pasada
     */
    public function esPasada(): bool
    {
        return $this->fecha < new DateTime();
    }

    /**
     * Notifica un recordatorio de la cita
     */
    public function notificarRecordatorio(): void
    {
        // Implementación de notificación
    }

    // Getters
    public function getIdCita(): int
    {
        return $this->idCita;
    }

    public function getPaciente(): Paciente
    {
        return $this->paciente;
    }

    public function getMedico(): Medico
    {
        return $this->medico;
    }

    public function getFecha(): DateTime
    {
        return $this->fecha;
    }

    public function getHora(): string
    {
        return $this->hora;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getMotivo(): string
    {
        return $this->motivo;
    }

    // Setters
    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }
}
