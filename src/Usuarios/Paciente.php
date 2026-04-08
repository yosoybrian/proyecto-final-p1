<?php

declare(strict_types=1);

namespace CitasMedicas\Usuarios;

use CitasMedicas\Usuario;
use CitasMedicas\Dominio\Cita;
use CitasMedicas\Dominio\HistorialMedico;
use DateTime;

/**
 * Clase Paciente
 */
class Paciente extends Usuario
{
    public function __construct(
        int $id,
        string $nombre,
        string $apellido,
        string $email,
        string $telefono,
        string $username,
        string $password,
        DateTime $fechaRegistro,
        bool $activo,
        private int $idPaciente,
        private DateTime $fechaNacimiento,
        private ?HistorialMedico $historialMedico = null
    ) {
        parent::__construct(
            $id,
            $nombre,
            $apellido,
            $email,
            $telefono,
            $username,
            $password,
            $fechaRegistro,
            $activo
        );
    }

    /**
     * Agenda una cita médica
     */
    public function agendarCita(Medico $medico, DateTime $fecha, string $hora, string $motivo): Cita
    {
        return new Cita(
            rand(1, 10000),
            $this,
            $medico,
            $fecha,
            $hora,
            'pendiente',
            $motivo
        );
    }

    /**
     * Cancela una cita
     */
    public function cancelarCita(int $idCita): bool
    {
        return true;
    }

    /**
     * Ver historial de citas
     */
    public function verHistorialCitas(): array
    {
        return [];
    }

    /**
     * Ver historial médico
     */
    public function verHistorialMedico(): ?HistorialMedico
    {
        return $this->historialMedico;
    }

    /**
     * Edita el perfil del paciente
     */
    public function editarPerfil(string $email, string $telefono): bool
    {
        $this->setEmail($email);
        $this->setTelefono($telefono);
        return true;
    }

    /**
     * Envía una notificación al destinatario especificado
     */
    public function enviarNotificacion(string $mensaje, string $destinatario): bool
    {
        return true;
    }

    /**
     * Valida el paciente
     */
    public function validar(): bool
    {
        return !empty($this->getNombre()) && !empty($this->getEmail());
    }

    // Getters
    public function getIdPaciente(): int
    {
        return $this->idPaciente;
    }

    public function getFechaNacimiento(): DateTime
    {
        return $this->fechaNacimiento;
    }

    public function getHistorialMedico(): ?HistorialMedico
    {
        return $this->historialMedico;
    }

    // Setters
    public function setHistorialMedico(?HistorialMedico $historialMedico): void
    {
        $this->historialMedico = $historialMedico;
    }
}
