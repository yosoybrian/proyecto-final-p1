<?php

declare(strict_types=1);

namespace CitasMedicas\Dominio;

use CitasMedicas\Usuarios\Paciente;

/**
 * Clase HistorialMedico
 */
class HistorialMedico
{
    public function __construct(
        private int $id,
        private Paciente $paciente,
        private array $diagnosticos,
        private string $alergias,
        private string $grupoSanguineo
    ) {}

    /**
     * Agrega un diagnóstico al historial
     */
    public function agregarDiagnostico(Diagnostico $d): void
    {
        $this->diagnosticos[] = $d;
    }

    /**
     * Obtiene un resumen del historial médico
     */
    public function obtenerResumen(): string
    {
        return "Historial médico del paciente #{$this->paciente->getId()}";
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getPaciente(): Paciente
    {
        return $this->paciente;
    }

    public function getDiagnosticos(): array
    {
        return $this->diagnosticos;
    }

    public function getAlergias(): string
    {
        return $this->alergias;
    }

    public function getGrupoSanguineo(): string
    {
        return $this->grupoSanguineo;
    }

    // Setters
    public function setAlergias(string $alergias): void
    {
        $this->alergias = $alergias;
    }

    public function setGrupoSanguineo(string $grupoSanguineo): void
    {
        $this->grupoSanguineo = $grupoSanguineo;
    }
}
