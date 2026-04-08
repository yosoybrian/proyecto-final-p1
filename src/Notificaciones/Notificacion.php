<?php

declare(strict_types=1);

namespace CitasMedicas\Notificaciones;

use DateTime;

/**
 * Clase base Notificacion
 */
class Notificacion
{
    public function __construct(
        private int $id,
        private string $destinatario,
        private string $mensaje,
        private bool $leida,
        private DateTime $fechaEnvio
    ) {}

    /**
     * Marca la notificación como leída
     */
    public function marcarComoLeida(): void
    {
        $this->leida = true;
    }

    /**
     * Reenvía la notificación
     */
    public function reenviar(): bool
    {
        return true;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getDestinatario(): string
    {
        return $this->destinatario;
    }

    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    public function isLeida(): bool
    {
        return $this->leida;
    }

    public function getFechaEnvio(): DateTime
    {
        return $this->fechaEnvio;
    }

    // Setters
    public function setDestinatario(string $destinatario): void
    {
        $this->destinatario = $destinatario;
    }

    public function setMensaje(string $mensaje): void
    {
        $this->mensaje = $mensaje;
    }

    public function setLeida(bool $leida): void
    {
        $this->leida = $leida;
    }
}
