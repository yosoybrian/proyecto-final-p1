<?php

declare(strict_types=1);

namespace ApiEjercicio\Controladores;

use ApiEjercicio\Repositorios\PacienteRepository;

class PacienteController
{
    private PacienteRepository $repository;

    public function __construct()
    {
        $this->repository = new PacienteRepository();
    }

    public function index(): void
    {
        try {
            $pacientes = $this->repository->obtenerTodos();
            $this->responderJSON($pacientes);
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
            $paciente = $this->repository->obtenerPorId($id);

            if ($paciente === false) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Paciente no encontrado',
                ], 404);

                return;
            }

            $this->responderJSON($paciente);
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
            $id = $this->repository->crear($datos);
            $paciente = $this->repository->obtenerPorId($id);

            $this->responderJSON($paciente ?? ['id' => $id], 201);
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
            $pacienteExistente = $this->repository->obtenerPorId($id);

            if ($pacienteExistente === false) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Paciente no encontrado',
                ], 404);

                return;
            }

            $datos = $this->obtenerDatosJson();
            $this->repository->actualizar($id, $datos);
            $paciente = $this->repository->obtenerPorId($id);

            $this->responderJSON($paciente ?? ['id' => $id]);
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
                    'message' => 'Paciente no encontrado',
                ], 404);

                return;
            }

            $this->repository->eliminar($id);
            $this->responderJSON([
                'success' => true,
                'message' => 'Paciente eliminado',
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