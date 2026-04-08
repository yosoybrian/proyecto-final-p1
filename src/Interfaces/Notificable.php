<?php

declare(strict_types=1);

namespace CitasMedicas\Interfaces;

/**
 * Interfaz Notificable
 *
 * Contrato para las clases que implementen sistema de notificaciones.
 */
interface Notificable
{
    /**
     * Envía una notificación al destinatario especificado.
     *
     * @param string $mensaje El mensaje a enviar
     * @param string $destinatario El destinatario de la notificación
     * @return bool True si el envío fue exitoso, false en caso contrario
     */
    public function enviarNotificacion(string $mensaje, string $destinatario): bool;
}
