<?php

declare(strict_types=1);

namespace CitasMedicas\Notificaciones;

use DateTime;

/**
 * Clase SMSNotificacion
 */
class SMSNotificacion extends Notificacion
{
    public function __construct(
        int $id,
        string $destinatario,
        string $mensaje,
        bool $leida,
        DateTime $fechaEnvio,
        private string $numeroDestino
    ) {
        parent::__construct($id, $destinatario, $mensaje, $leida, $fechaEnvio);
    }

    /**
     * Envía una notificación por SMS
     */
    public function enviarNotificacion(string $mensaje): bool
    {
        return true;
    }

    // Getter
    public function getNumeroDestino(): string
    {
        return $this->numeroDestino;
    }

    // Setter
    public function setNumeroDestino(string $numeroDestino): void
    {
        $this->numeroDestino = $numeroDestino;
    }
}
