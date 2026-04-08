<?php

declare(strict_types=1);

namespace CitasMedicas\Usuarios;

use CitasMedicas\Usuario;
use CitasMedicas\Dominio\Especialidad;
use DateTime;

/**
 * Clase Administrador
 */
class Administrador extends Usuario
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
        private int $idAdmin
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
     * Crea un médico
     */
    public function crearMedico(
        int $id,
        string $nombre,
        string $apellido,
        string $email,
        string $telefono,
        string $username,
        string $password,
        DateTime $fechaRegistro,
        bool $activo,
        int $idMedico,
        Especialidad $especialidad,
        string $horarioInicio,
        string $horarioFin,
        string $numeroColegiado
    ): Medico {
        return new Medico(
            $id,
            $nombre,
            $apellido,
            $email,
            $telefono,
            $username,
            $password,
            $fechaRegistro,
            $activo,
            $idMedico,
            $especialidad,
            $horarioInicio,
            $horarioFin,
            $numeroColegiado
        );
    }

    /**
     * Elimina un usuario
     */
    public function eliminarUsuario(int $id): bool
    {
        return true;
    }

    /**
     * Gestiona especialidades
     */
    public function gestionarEspecialidades(): void
    {
        // Implementación de gestión de especialidades
    }

    /**
     * Audita el sistema
     */
    public function auditarSistema(): void
    {
        // Implementación de auditoría
    }

    /**
     * Envía una notificación al destinatario especificado
     */
    public function enviarNotificacion(string $mensaje, string $destinatario): bool
    {
        return true;
    }

    /**
     * Valida el administrador
     */
    public function validar(): bool
    {
        return !empty($this->getNombre()) && !empty($this->getEmail());
    }

    // Getter
    public function getIdAdmin(): int
    {
        return $this->idAdmin;
    }
}
