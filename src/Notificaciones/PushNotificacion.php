<?php

declare(strict_types=1);

namespace CitasMedicas\Notificaciones;

use DateTime;

/**
 * Clase PushNotificacion
 */
class PushNotificacion extends Notificacion
{
    public function __construct(
        int $id,
        string $destinatario,
        string $mensaje,
        bool $leida,
        DateTime $fechaEnvio,
        private string $dispositivoToken
    ) {
        parent::__construct($id, $destinatario, $mensaje, $leida, $fechaEnvio);
    }

    /**
     * Envía una notificación push
     */
    public function enviarNotificacion(string $mensaje): bool
    {
        return true;
    }

    // Getter
    public function getDispositivoToken(): string
    {
        return $this->dispositivoToken;
    }

    // Setter
    public function setDispositivoToken(string $dispositivoToken): void
    {
        $this->dispositivoToken = $dispositivoToken;
    }
}
