<?php

declare(strict_types=1);

namespace CitasMedicas;

/**
 * Clase abstracta Persona
 *
 * Clase base para todas las personas en el sistema
 */
abstract class Persona
{
    public function __construct(
        private int $id,
        private string $nombre,
        private string $apellido,
        private \DateTime $fechaNacimiento,
        private string $telefono,
        private string $email
    ) {}

    /**
     * Genera un ID único para la persona
     */
    protected function generarId(): string
    {
        return uniqid('PERSON_', true);
    }

    /**
     * Método abstracto para validar la persona
     */
    abstract public function validar(): bool;

    /**
     * Obtiene el nombre completo de la persona
     */
    public function getNombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getApellido(): string
    {
        return $this->apellido;
    }

    public function getFechaNacimiento(): \DateTime
    {
        return $this->fechaNacimiento;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    // Setters
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setApellido(string $apellido): void
    {
        $this->apellido = $apellido;
    }

    public function setFechaNacimiento(\DateTime $fechaNacimiento): void
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = $telefono;
    }

    /**
     * Cambia el correo electrónico de la persona
     *
     * Valida que el nuevo email tenga un formato correcto antes de actualizarlo.
     *
     * @param string $nuevo El nuevo correo electrónico
     * @return bool true si se cambió correctamente, false si el formato es inválido
     */
    public function cambiarEmail(string $nuevo): bool
    {
        // Validar formato del email
        if (filter_var($nuevo, FILTER_VALIDATE_EMAIL) !== false) {
            $this->email = $nuevo;
            return true;
        }

        return false;
    }
}
