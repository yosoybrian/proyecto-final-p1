<?php

declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestHelper;

/**
 * @group api
 * @group integration
 */
class IntegrationTest extends TestCase
{
    private string $exercisePath;
    private string $dbPath;

    protected function setUp(): void
    {
        $this->exercisePath = __DIR__ . '/../../exercises/api-ejercicio';
        $this->dbPath = $this->exercisePath . '/database.sqlite';

        TestHelper::resetDatabaseSingleton();
        TestHelper::cleanDatabase($this->dbPath);
    }

    protected function tearDown(): void
    {
        TestHelper::resetDatabaseSingleton();
        TestHelper::cleanDatabase($this->dbPath);
    }    public function testCompleteWorkflowPacienteAndCita(): void
    {
        $citaFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($citaFile) || !file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aun');
        }

        require_once $dbFile;
        require_once $pacienteFile;
        require_once $citaFile;

        \ApiEjercicio\Config\Database::getInstance();

        // 1. Crear paciente
        $pacienteRepo = new \ApiEjercicio\Repositorios\PacienteRepository();
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'email' => 'juan@example.com',
            'telefono' => '555-1234',
            'fecha_nacimiento' => '1990-01-01'
        ]);

        $this->assertGreaterThan(0, $pacienteId, '✅ Paciente creado correctamente');

        // 2. Crear cita para ese paciente
        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();
        $citaId = $citaRepo->crear([
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2024-12-15 10:00:00',
            'motivo' => 'Consulta general',
            'estado' => 'pendiente'
        ]);

        $this->assertGreaterThan(0, $citaId, '✅ Cita creada correctamente');

        // 3. Obtener la cita con datos del paciente (JOIN)
        $cita = $citaRepo->obtenerPorId($citaId);

        $this->assertEquals('Juan', $cita['paciente_nombre'], '✅ JOIN trae datos del paciente');
        $this->assertEquals('Perez', $cita['paciente_apellido']);
        $this->assertEquals('Consulta general', $cita['motivo']);

        // 4. Actualizar estado de la cita
        $citaRepo->actualizar($citaId, [
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2024-12-15 10:00:00',
            'motivo' => 'Consulta general',
            'estado' => 'confirmada'
        ]);

        $citaActualizada = $citaRepo->obtenerPorId($citaId);
        $this->assertEquals('confirmada', $citaActualizada['estado'], '✅ Cita actualizada');

        // 5. Listar todas las citas del paciente
        $todasLasCitas = $citaRepo->obtenerTodos();
        $this->assertCount(1, $todasLasCitas, '✅ Lista correcta de citas');

        // 6. Eliminar la cita
        $citaRepo->eliminar($citaId);
        $citaEliminada = $citaRepo->obtenerPorId($citaId);
        $this->assertFalse($citaEliminada, '✅ Cita eliminada correctamente');

        // 7. Eliminar el paciente
        $pacienteRepo->eliminar($pacienteId);
        $pacienteEliminado = $pacienteRepo->obtenerPorId($pacienteId);
        $this->assertFalse($pacienteEliminado, '✅ Paciente eliminado correctamente');
    }

    public function testMultiplePacientesAndCitas(): void
    {
        $citaFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($citaFile) || !file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aun');
        }

        require_once $dbFile;
        require_once $pacienteFile;
        require_once $citaFile;

        \ApiEjercicio\Config\Database::getInstance();

        $pacienteRepo = new \ApiEjercicio\Repositorios\PacienteRepository();
        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();

        // Crear 3 pacientes
        $paciente1 = $pacienteRepo->crear([
            'nombre' => 'Maria',
            'apellido' => 'Garcia',
            'email' => 'maria@example.com'
        ]);

        $paciente2 = $pacienteRepo->crear([
            'nombre' => 'Carlos',
            'apellido' => 'Lopez',
            'email' => 'carlos@example.com'
        ]);

        $paciente3 = $pacienteRepo->crear([
            'nombre' => 'Ana',
            'apellido' => 'Martinez',
            'email' => 'ana@example.com'
        ]);

        // Crear citas para cada paciente
        $citaRepo->crear([
            'paciente_id' => $paciente1,
            'fecha_hora' => '2024-12-15 09:00:00',
            'motivo' => 'Consulta Maria'
        ]);

        $citaRepo->crear([
            'paciente_id' => $paciente2,
            'fecha_hora' => '2024-12-15 10:00:00',
            'motivo' => 'Consulta Carlos'
        ]);

        $citaRepo->crear([
            'paciente_id' => $paciente3,
            'fecha_hora' => '2024-12-15 11:00:00',
            'motivo' => 'Consulta Ana'
        ]);

        // Crear segunda cita para paciente1
        $citaRepo->crear([
            'paciente_id' => $paciente1,
            'fecha_hora' => '2024-12-16 14:00:00',
            'motivo' => 'Segunda consulta Maria'
        ]);

        // Verificar totales
        $pacientes = $pacienteRepo->obtenerTodos();
        $this->assertCount(3, $pacientes, '✅ 3 pacientes creados');

        $citas = $citaRepo->obtenerTodos();
        $this->assertCount(4, $citas, '✅ 4 citas creadas');

        // Verificar que cada cita tiene informacion del paciente
        foreach ($citas as $cita) {
            $this->assertArrayHasKey('paciente_nombre', $cita, '✅ JOIN funciona en listado');
            $this->assertArrayHasKey('paciente_apellido', $cita);
        }
    }

    public function testDatabasePersistence(): void
    {
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aun');
        }

        require_once $dbFile;
        require_once $pacienteFile;

        // Primera conexion: crear datos
        $db1 = \ApiEjercicio\Config\Database::getInstance();
        $repo1 = new \ApiEjercicio\Repositorios\PacienteRepository();

        $id = $repo1->crear([
            'nombre' => 'Persistente',
            'apellido' => 'Dato',
            'email' => 'persistente@example.com'
        ]);

        // Simular nueva conexion obteniendo nueva instancia
        $db2 = \ApiEjercicio\Config\Database::getInstance();
        $repo2 = new \ApiEjercicio\Repositorios\PacienteRepository();

        $paciente = $repo2->obtenerPorId($id);

        $this->assertIsArray($paciente, '✅ Los datos persisten entre conexiones');
        $this->assertEquals('Persistente', $paciente['nombre']);
    }

    public function testUpdatePreservesOtherFields(): void
    {
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aun');
        }

        require_once $dbFile;
        require_once $pacienteFile;

        \ApiEjercicio\Config\Database::getInstance();
        $repo = new \ApiEjercicio\Repositorios\PacienteRepository();

        // Crear con todos los campos
        $id = $repo->crear([
            'nombre' => 'Original',
            'apellido' => 'Completo',
            'email' => 'original@example.com',
            'telefono' => '555-1111',
            'fecha_nacimiento' => '1990-01-01'
        ]);

        // Actualizar solo email
        $repo->actualizar($id, [
            'nombre' => 'Original',
            'apellido' => 'Completo',
            'email' => 'nuevo@example.com',
            'telefono' => '555-1111',
            'fecha_nacimiento' => '1990-01-01'
        ]);

        $paciente = $repo->obtenerPorId($id);

        $this->assertEquals('nuevo@example.com', $paciente['email'], '✅ Campo actualizado');
        $this->assertEquals('Original', $paciente['nombre'], '✅ Otros campos preservados');
        $this->assertEquals('555-1111', $paciente['telefono']);
    }

    public function testDeleteDoesNotAffectOtherRecords(): void
    {
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aun');
        }

        require_once $dbFile;
        require_once $pacienteFile;

        \ApiEjercicio\Config\Database::getInstance();
        $repo = new \ApiEjercicio\Repositorios\PacienteRepository();

        $id1 = $repo->crear([
            'nombre' => 'Paciente1',
            'apellido' => 'Mantener',
            'email' => 'paciente1@example.com'
        ]);

        $id2 = $repo->crear([
            'nombre' => 'Paciente2',
            'apellido' => 'Eliminar',
            'email' => 'paciente2@example.com'
        ]);

        $id3 = $repo->crear([
            'nombre' => 'Paciente3',
            'apellido' => 'Mantener',
            'email' => 'paciente3@example.com'
        ]);

        // Eliminar paciente2
        $repo->eliminar($id2);

        // Verificar que solo paciente2 fue eliminado
        $paciente1 = $repo->obtenerPorId($id1);
        $paciente2 = $repo->obtenerPorId($id2);
        $paciente3 = $repo->obtenerPorId($id3);

        $this->assertIsArray($paciente1, '✅ Otros registros no afectados');
        $this->assertFalse($paciente2, '✅ Registro correcto eliminado');
        $this->assertIsArray($paciente3, '✅ Otros registros no afectados');
    }
}
