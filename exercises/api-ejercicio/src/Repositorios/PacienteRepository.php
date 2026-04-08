<?php

declare(strict_types=1);

namespace ApiEjercicio\Repositorios;

use ApiEjercicio\Config\Database;
use InvalidArgumentException;
use PDO;

class PacienteRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConexion();
    }

    public function obtenerTodos(): array
    {
        $stmt = $this->db->query('SELECT * FROM pacientes ORDER BY id DESC');

        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM pacientes WHERE id = ?');
        $stmt->execute([$id]);

        $paciente = $stmt->fetch();

        return $paciente === false ? false : $paciente;
    }

    public function crear($datos)
    {
        $datos = $this->normalizarDatos($datos);

        $stmt = $this->db->prepare(
            'INSERT INTO pacientes (nombre, apellido, email, telefono, fecha_nacimiento)
             VALUES (?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['telefono'],
            $datos['fecha_nacimiento'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function actualizar($id, $datos)
    {
        $datos = $this->normalizarDatos($datos);

        $stmt = $this->db->prepare(
            'UPDATE pacientes
             SET nombre = ?, apellido = ?, email = ?, telefono = ?, fecha_nacimiento = ?
             WHERE id = ?'
        );

        return $stmt->execute([
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['telefono'],
            $datos['fecha_nacimiento'],
            $id,
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare('DELETE FROM pacientes WHERE id = ?');

        return $stmt->execute([$id]);
    }

    private function normalizarDatos($datos): array
    {
        if (!is_array($datos)) {
            throw new InvalidArgumentException('Los datos del paciente deben ser un arreglo');
        }

        $nombre = $this->validarTextoObligatorio($datos, 'nombre');
        $apellido = $this->validarTextoObligatorio($datos, 'apellido');
        $email = $this->validarEmail($datos, 'email');

        return [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'telefono' => $this->normalizarOpcional($datos['telefono'] ?? null),
            'fecha_nacimiento' => $this->normalizarOpcional($datos['fecha_nacimiento'] ?? null),
        ];
    }

    private function validarTextoObligatorio(array $datos, string $clave): string
    {
        if (!array_key_exists($clave, $datos)) {
            throw new InvalidArgumentException('Falta el campo obligatorio: ' . $clave);
        }

        $valor = trim((string) $datos[$clave]);

        if ($valor === '') {
            throw new InvalidArgumentException('El campo ' . $clave . ' no puede estar vacio');
        }

        return $valor;
    }

    private function validarEmail(array $datos, string $clave): string
    {
        $email = $this->validarTextoObligatorio($datos, $clave);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Email invalido');
        }

        return $email;
    }

    private function normalizarOpcional($valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $valor = trim((string) $valor);

        return $valor === '' ? null : $valor;
    }
}