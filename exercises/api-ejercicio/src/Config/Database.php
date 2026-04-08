<?php

declare(strict_types=1);

namespace ApiEjercicio\Config;

use PDO;

class Database
{
    private static ?self $instancia = null;
    private PDO $conexion;

    private function __construct()
    {
        $dbPath = __DIR__ . '/../../database.sqlite';

        $this->conexion = new PDO('sqlite:' . $dbPath);
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->conexion->exec('PRAGMA foreign_keys = ON');

        $this->crearTablas();
    }

    public static function getInstance(): self
    {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }

        return self::$instancia;
    }

    public function getConexion(): PDO
    {
        return $this->conexion;
    }

    private function crearTablas(): void
    {
        $this->conexion->exec(
            "CREATE TABLE IF NOT EXISTS pacientes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                apellido TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                telefono TEXT,
                fecha_nacimiento DATE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )"
        );

        $this->conexion->exec(
            "CREATE TABLE IF NOT EXISTS citas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                paciente_id INTEGER NOT NULL,
                fecha_hora DATETIME NOT NULL,
                motivo TEXT NOT NULL,
                estado TEXT NOT NULL DEFAULT 'pendiente',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
            )"
        );
    }
}