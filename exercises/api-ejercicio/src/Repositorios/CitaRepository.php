<?php

declare(strict_types=1);

namespace ApiEjercicio\Repositorios;

use ApiEjercicio\Config\Database;
use InvalidArgumentException;
use PDO;

class CitaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConexion();
    }

    public function obtenerTodos(): array
    {
        $stmt = $this->db->query(
            'SELECT c.*, p.nombre as paciente_nombre, p.apellido as paciente_apellido, p.email as paciente_email
             FROM citas c
             LEFT JOIN pacientes p ON c.paciente_id = p.id
             ORDER BY c.fecha_hora DESC'
        );

        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, p.nombre as paciente_nombre, p.apellido as paciente_apellido, p.email as paciente_email
             FROM citas c
             LEFT JOIN pacientes p ON c.paciente_id = p.id
             WHERE c.id = ?'
        );
        $stmt->execute([$id]);

        $cita = $stmt->fetch();

        return $cita === false ? false : $cita;
    }

    public function crear($datos)
    {
        $datos = $this->normalizarDatos($datos, true);

        $stmt = $this->db->prepare(
            'INSERT INTO citas (paciente_id, fecha_hora, motivo, estado)
             VALUES (?, ?, ?, ?)'
        );

        $stmt->execute([
            $datos['paciente_id'],
            $datos['fecha_hora'],
            $datos['motivo'],
            $datos['estado'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function actualizar($id, $datos)
    {
        $datos = $this->normalizarDatos($datos, false);

        $stmt = $this->db->prepare(
            'UPDATE citas
             SET paciente_id = ?, fecha_hora = ?, motivo = ?, estado = ?
             WHERE id = ?'
        );

        return $stmt->execute([
            $datos['paciente_id'],
            $datos['fecha_hora'],
            $datos['motivo'],
            $datos['estado'],
            $id,
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare('DELETE FROM citas WHERE id = ?');

        return $stmt->execute([$id]);
    }

    private function normalizarDatos($datos, bool $aplicarEstadoPorDefecto): array
    {
        if (!is_array($datos)) {
            throw new InvalidArgumentException('Los datos de la cita deben ser un arreglo');
        }

        if (!array_key_exists('paciente_id', $datos) || trim((string) $datos['paciente_id']) === '') {
            throw new InvalidArgumentException('Falta el campo obligatorio: paciente_id');
        }

        if (!array_key_exists('fecha_hora', $datos) || trim((string) $datos['fecha_hora']) === '') {
            throw new InvalidArgumentException('Falta el campo obligatorio: fecha_hora');
        }

        if (!array_key_exists('motivo', $datos) || trim((string) $datos['motivo']) === '') {
            throw new InvalidArgumentException('Falta el campo obligatorio: motivo');
        }

        $fechaHora = trim((string) $datos['fecha_hora']);
        $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fechaHora);
        $errores = \DateTime::getLastErrors() ?: ['warning_count' => 0, 'error_count' => 0];

        if (
            $fecha === false
            || $errores['warning_count'] > 0
            || $errores['error_count'] > 0
            || $fecha->format('Y-m-d H:i:s') !== $fechaHora
        ) {
            throw new InvalidArgumentException('Formato de fecha invalido');
        }

        $estado = $this->normalizarEstado($datos['estado'] ?? null, $aplicarEstadoPorDefecto);

        return [
            'paciente_id' => (int) $datos['paciente_id'],
            'fecha_hora' => $fechaHora,
            'motivo' => trim((string) $datos['motivo']),
            'estado' => $estado,
        ];
    }

    private function normalizarEstado($estado, bool $aplicarEstadoPorDefecto): string
    {
        $estadosValidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];

        if ($estado === null || trim((string) $estado) === '') {
            if ($aplicarEstadoPorDefecto) {
                return 'pendiente';
            }

            throw new InvalidArgumentException('Falta el campo obligatorio: estado');
        }

        $estado = trim((string) $estado);

        if (!in_array($estado, $estadosValidos, true)) {
            throw new InvalidArgumentException('Estado invalido');
        }

        return $estado;
    }
}