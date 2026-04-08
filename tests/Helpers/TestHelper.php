<?php

declare(strict_types=1);

namespace Tests\Helpers;

/**
 * TestHelper - Utilidades para los tests
 */
class TestHelper
{
    /**
     * Resetear el Singleton de Database para tests
     *
     * Usa Reflection para forzar que getInstance() cree una nueva instancia
     */
    public static function resetDatabaseSingleton(): void
    {
        if (!class_exists('ApiEjercicio\Config\Database')) {
            return;
        }

        try {
            $reflection = new \ReflectionClass('ApiEjercicio\Config\Database');
            $instance = $reflection->getProperty('instancia');
            $instance->setAccessible(true);
            $instance->setValue(null, null);
        } catch (\Exception $e) {
            // Ignorar si no se puede resetear
        }
    }

    /**
     * Limpiar base de datos SQLite de tests
     *
     * @param string $dbPath Ruta al archivo de base de datos
     */
    public static function cleanDatabase(string $dbPath): void
    {
        if (file_exists($dbPath)) {
            // Cerrar posibles conexiones
            self::resetDatabaseSingleton();

            // Esperar un momento para que se libere el archivo
            usleep(100000); // 100ms

            // Intentar eliminar
            @unlink($dbPath);

            // Si no se pudo eliminar, intentar truncar
            if (file_exists($dbPath)) {
                @file_put_contents($dbPath, '');
                @unlink($dbPath);
            }
        }
    }
}
