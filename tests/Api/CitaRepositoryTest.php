<?php

declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestHelper;

/**
 * @group api
 * @group repository
 * @group cita
 */
class CitaRepositoryTest extends TestCase
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
    }    public function testCitaRepositoryFileExists(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $this->assertFileExists(
            $classFile,
            '❌ El archivo CitaRepository.php debe existir en src/Repositorios/'
        );
    }

    public function testCitaRepositoryUsesCorrectNamespace(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('CitaRepository.php no existe aún');
        }

        $content = file_get_contents($classFile);

        $this->assertStringContainsString(
            'namespace ApiEjercicio\Repositorios',
            $content,
            '❌ CitaRepository debe usar el namespace ApiEjercicio\Repositorios'
        );
    }

    public function testCitaRepositoryHasRequiredMethods(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('CitaRepository.php no existe aún');
        }

        require_once $this->exercisePath . '/src/Config/Database.php';
        require_once $classFile;

        $reflection = new \ReflectionClass('ApiEjercicio\Repositorios\CitaRepository');

        $requiredMethods = ['obtenerTodos', 'obtenerPorId', 'crear', 'actualizar', 'eliminar'];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                $reflection->hasMethod($method),
                "❌ CitaRepository debe tener el método {$method}()"
            );
        }
    }

    public function testCitaRepositoryCrearMethod(): void
    {
        $citaFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($citaFile) || !file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $pacienteFile;
        require_once $citaFile;

        \ApiEjercicio\Config\Database::getInstance();

        // Crear un paciente primero
        $pacienteRepo = new \ApiEjercicio\Repositorios\PacienteRepository();
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Test',
            'apellido' => 'Paciente',
            'email' => 'paciente@test.com',
            'telefono' => '555-0000',
            'fecha_nacimiento' => '1990-01-01'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();

        $id = $citaRepo->crear([
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2024-12-15 10:00:00',
            'motivo' => 'Consulta general',
            'estado' => 'pendiente'
        ]);

        $this->assertIsInt($id, '❌ El método crear() debe retornar un entero (ID)');
        $this->assertGreaterThan(0, $id, '❌ El ID retornado debe ser mayor que 0');
    }

    public function testCitaRepositoryObtenerTodosWithJoin(): void
    {
        $citaFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($citaFile) || !file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $pacienteFile;
        require_once $citaFile;

        \ApiEjercicio\Config\Database::getInstance();

        $pacienteRepo = new \ApiEjercicio\Repositorios\PacienteRepository();
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'María',
            'apellido' => 'López',
            'email' => 'maria@test.com',
            'telefono' => '555-1111',
            'fecha_nacimiento' => '1985-05-15'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();
        $citaRepo->crear([
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2024-12-20 14:00:00',
            'motivo' => 'Revisión',
            'estado' => 'confirmada'
        ]);

        $citas = $citaRepo->obtenerTodos();

        $this->assertIsArray($citas, '❌ obtenerTodos() debe retornar un array');
        $this->assertGreaterThan(0, count($citas), '❌ Debe haber al menos una cita');

        // Verificar que incluye información del paciente (JOIN)
        $this->assertArrayHasKey(
            'paciente_nombre',
            $citas[0],
            '❌ obtenerTodos() debe incluir paciente_nombre (usar LEFT JOIN)'
        );

        $this->assertEquals('María', $citas[0]['paciente_nombre'], '❌ El nombre del paciente debe estar incluido');
    }

    public function testCitaRepositoryObtenerPorIdWithJoin(): void
    {
        $citaFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($citaFile) || !file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $pacienteFile;
        require_once $citaFile;

        \ApiEjercicio\Config\Database::getInstance();

        $pacienteRepo = new \ApiEjercicio\Repositorios\PacienteRepository();
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Carlos',
            'apellido' => 'Ruiz',
            'email' => 'carlos@test.com',
            'telefono' => '555-2222',
            'fecha_nacimiento' => '1992-08-25'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();
        $citaId = $citaRepo->crear([
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2024-12-25 09:00:00',
            'motivo' => 'Chequeo anual',
            'estado' => 'pendiente'
        ]);

        $cita = $citaRepo->obtenerPorId($citaId);

        $this->assertIsArray($cita, '❌ obtenerPorId() debe retornar un array');
        $this->assertArrayHasKey('paciente_nombre', $cita, '❌ Debe incluir información del paciente');
        $this->assertEquals('Carlos', $cita['paciente_nombre']);
    }

    public function testCitaRepositoryActualizarMethod(): void
    {
        $citaFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($citaFile) || !file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $pacienteFile;
        require_once $citaFile;

        \ApiEjercicio\Config\Database::getInstance();

        $pacienteRepo = new \ApiEjercicio\Repositorios\PacienteRepository();
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Ana',
            'apellido' => 'Torres',
            'email' => 'ana@test.com',
            'telefono' => '555-3333',
            'fecha_nacimiento' => '1988-03-10'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();
        $citaId = $citaRepo->crear([
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2024-12-30 11:00:00',
            'motivo' => 'Consulta original',
            'estado' => 'pendiente'
        ]);

        $resultado = $citaRepo->actualizar($citaId, [
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2024-12-30 15:00:00',
            'motivo' => 'Consulta modificada',
            'estado' => 'confirmada'
        ]);

        $this->assertTrue($resultado, '❌ actualizar() debe retornar true');

        $cita = $citaRepo->obtenerPorId($citaId);
        $this->assertEquals('Consulta modificada', $cita['motivo']);
        $this->assertEquals('confirmada', $cita['estado']);
    }

    public function testCitaRepositoryEliminarMethod(): void
    {
        $citaFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($citaFile) || !file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $pacienteFile;
        require_once $citaFile;

        \ApiEjercicio\Config\Database::getInstance();

        $pacienteRepo = new \ApiEjercicio\Repositorios\PacienteRepository();
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Luis',
            'apellido' => 'Martínez',
            'email' => 'luis@test.com',
            'telefono' => '555-4444',
            'fecha_nacimiento' => '1980-12-01'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();
        $citaId = $citaRepo->crear([
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2025-01-05 10:00:00',
            'motivo' => 'Para eliminar',
            'estado' => 'pendiente'
        ]);

        $resultado = $citaRepo->eliminar($citaId);

        $this->assertTrue($resultado, '❌ eliminar() debe retornar true');

        $cita = $citaRepo->obtenerPorId($citaId);
        $this->assertFalse($cita, '❌ La cita eliminada no debe encontrarse');
    }

    public function testCitaRepositoryDefaultEstadoIsPendiente(): void
    {
        $citaFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $pacienteFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($citaFile) || !file_exists($pacienteFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $pacienteFile;
        require_once $citaFile;

        \ApiEjercicio\Config\Database::getInstance();

        $pacienteRepo = new \ApiEjercicio\Repositorios\PacienteRepository();
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Pedro',
            'apellido' => 'Sánchez',
            'email' => 'pedro@test.com',
            'telefono' => '555-5555',
            'fecha_nacimiento' => '1975-07-15'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();

        // Crear sin especificar estado
        $citaId = $citaRepo->crear([
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2025-01-10 16:00:00',
            'motivo' => 'Sin estado'
        ]);

        $cita = $citaRepo->obtenerPorId($citaId);

        $this->assertEquals(
            'pendiente',
            $cita['estado'],
            '❌ El estado por defecto debe ser "pendiente" (usar operador ??)'
        );
    }
}
