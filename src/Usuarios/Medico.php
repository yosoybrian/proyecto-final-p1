<?php

declare(strict_types=1);

namespace CitasMedicas\Usuarios;

use CitasMedicas\Usuario;
use CitasMedicas\Dominio\Cita;
use CitasMedicas\Dominio\Diagnostico;
use CitasMedicas\Dominio\Especialidad;
use CitasMedicas\Dominio\HistorialMedico;
use DateTime;

/**
 * Clase Medico
 */
class Medico extends Usuario
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
        private int $idMedico,
        private Especialidad $especialidad,
        private string $horarioInicio,
        private string $horarioFin,
        private string $numeroColegiado
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
     * Ver agenda del médico
     */
    public function verAgenda(DateTime $fecha): array
    {
        return [];
    }

    /**
     * Confirma la asistencia a una cita
     */
    public function confirmarAsistencia(Cita $cita): bool
    {
        return $cita->cambiarEstado('confirmada');
    }

    /**
     * Registra un diagnóstico
     */
    public function registrarDiagnostico(Cita $cita, string $desc, string $sintomas, string $tratamiento): Diagnostico
    {
        return new Diagnostico(
            rand(1, 10000),
            $cita,
            $desc,
            $sintomas,
            new DateTime(),
            $tratamiento,
            'en_tratamiento'
        );
    }

    /**
     * Actualiza un diagnóstico
     */
    public function actualizarDiagnostico(Diagnostico $d, array $datos): bool
    {
        return true;
    }

    /**
     * Ver historial del paciente
     */
    public function verHistorialPaciente(Paciente $p): ?HistorialMedico
    {
        return $p->getHistorialMedico();
    }

    /**
     * Envía una notificación al destinatario especificado
     */
    public function enviarNotificacion(string $mensaje, string $destinatario): bool
    {
        return true;
    }

    /**
     * Valida el médico
     */
    public function validar(): bool
    {
        return !empty($this->getNombre()) && !empty($this->numeroColegiado);
    }

    // Getters
    public function getIdMedico(): int
    {
        return $this->idMedico;
    }

    public function getEspecialidad(): Especialidad
    {
        return $this->especialidad;
    }

    public function getHorarioInicio(): string
    {
        return $this->horarioInicio;
    }

    public function getHorarioFin(): string
    {
        return $this->horarioFin;
    }

    public function getNumeroColegiado(): string
    {
        return $this->numeroColegiado;
    }

    // Setters
    public function setEspecialidad(Especialidad $especialidad): void
    {
        $this->especialidad = $especialidad;
    }

    public function setHorarioInicio(string $horarioInicio): void
    {
        $this->horarioInicio = $horarioInicio;
    }

    public function setHorarioFin(string $horarioFin): void
    {
        $this->horarioFin = $horarioFin;
    }
}
