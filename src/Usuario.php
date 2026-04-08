<?php

declare(strict_types=1);

namespace CitasMedicas;

use CitasMedicas\Interfaces\Notificable;
use DateTime;

/**
 * Clase abstracta Usuario
 *
 * Extiende Persona e implementa Notificable
 */
abstract class Usuario extends Persona implements Notificable
{
    public function __construct(
        int $id,
        string $nombre,
        string $apellido,
        DateTime $fechaNacimiento,
        string $telefono,
        string $email,
        private string $username,
        private string $password,
        private DateTime $fechaRegistro,
        private bool $activo = true
    ) {
        parent::__construct($id, $nombre, $apellido, $fechaNacimiento, $telefono, $email);
    }

    /**
     * Inicia sesión del usuario
     */
    public function login(string $username, string $password): bool
    {
        return $this->username === $username && $this->password === $password;
    }

    /**
     * Cierra sesión del usuario
     */
    public function logout(): void
    {
        // Implementación de logout
    }

    /**
     * Cambia la contraseña del usuario
     */
    public function cambiarPassword(string $actual, string $nueva): bool
    {
        if ($this->password === $actual) {
            $this->password = $nueva;
            return true;
        }
        return false;
    }

    /**
     * Verifica si el usuario está activo
     */
    public function esActivo(): bool
    {
        return $this->activo;
    }

    // Getters
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFechaRegistro(): DateTime
    {
        return $this->fechaRegistro;
    }

    public function getActivo(): bool
    {
        return $this->activo;
    }

    // Setters
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }
}
