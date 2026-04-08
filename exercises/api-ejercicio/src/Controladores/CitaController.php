<?php

declare(strict_types=1);

namespace ApiEjercicio\Controladores;

use ApiEjercicio\Repositorios\CitaRepository;
use ApiEjercicio\Repositorios\PacienteRepository;

class CitaController
{
    private CitaRepository $repository;
    private PacienteRepository $pacienteRepository;

    public function __construct()
    {
        $this->repository = new CitaRepository();
        $this->pacienteRepository = new PacienteRepository();
    }

    public function index(): void
    {
        try {
            $citas = $this->repository->obtenerTodos();
            $this->responderJSON($citas);
        } catch (\Throwable $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): void
    {
        try {
            $cita = $this->repository->obtenerPorId($id);

            if ($cita === false) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Cita no encontrada',
                ], 404);

                return;
            }

            $this->responderJSON($cita);
        } catch (\Throwable $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(): void
    {
        try {
            $datos = $this->obtenerDatosJson();

            if (!isset($datos['paciente_id']) || !isset($datos['fecha_hora']) || !isset($datos['motivo'])) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Faltan campos: paciente_id, fecha_hora, motivo',
                ], 400);

                return;
            }

            if ($this->pacienteRepository->obtenerPorId($datos['paciente_id']) === false) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'El paciente no existe',
                ], 404);

                return;
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
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Formato de fecha invalido. Use: YYYY-MM-DD HH:MM:SS',
                ], 400);

                return;
            }

            $id = $this->repository->crear($datos);
            $cita = $this->repository->obtenerPorId($id);

            $this->responderJSON($cita ?? ['id' => $id], 201);
        } catch (\InvalidArgumentException $e) {
            $this->responderJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update($id): void
    {
        try {
            if ($this->repository->obtenerPorId($id) === false) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Cita no encontrada',
                ], 404);

                return;
            }

            $datos = $this->obtenerDatosJson();

            if (!isset($datos['paciente_id']) || !isset($datos['fecha_hora']) || !isset($datos['motivo'])) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Faltan campos: paciente_id, fecha_hora, motivo',
                ], 400);

                return;
            }

            if ($this->pacienteRepository->obtenerPorId($datos['paciente_id']) === false) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'El paciente no existe',
                ], 404);

                return;
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
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Formato de fecha invalido. Use: YYYY-MM-DD HH:MM:SS',
                ], 400);

                return;
            }

            $this->repository->actualizar($id, $datos);
            $cita = $this->repository->obtenerPorId($id);

            $this->responderJSON($cita ?? ['id' => $id]);
        } catch (\InvalidArgumentException $e) {
            $this->responderJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): void
    {
        try {
            if ($this->repository->obtenerPorId($id) === false) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Cita no encontrada',
                ], 404);

                return;
            }

            $this->repository->eliminar($id);

            $this->responderJSON([
                'success' => true,
                'message' => 'Cita eliminada',
                'id' => (int) $id,
            ]);
        } catch (\Throwable $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function responderJSON($data, $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    private function obtenerDatosJson(): array
    {
        $datos = json_decode((string) file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            throw new \InvalidArgumentException('JSON invalido');
        }

        return $datos;
    }
}