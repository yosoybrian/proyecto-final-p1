<?php

declare(strict_types=1);

namespace CitasMedicas\Notificaciones;

use DateTime;

/**
 * Clase EmailNotificacion
 */
class EmailNotificacion extends Notificacion
{
    public function __construct(
        int $id,
        string $destinatario,
        string $mensaje,
        bool $leida,
        DateTime $fechaEnvio,
        private string $asunto
    ) {
        parent::__construct($id, $destinatario, $mensaje, $leida, $fechaEnvio);
    }

    /**
     * Envía una notificación por email
     */
    public function enviarNotificacion(string $mensaje): bool
    {
        return true;
    }

    // Getter
    public function getAsunto(): string
    {
        return $this->asunto;
    }

    // Setter
    public function setAsunto(string $asunto): void
    {
        $this->asunto = $asunto;
    }
}
