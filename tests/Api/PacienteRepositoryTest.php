<?php

declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestHelper;

/**
 * @group api
 * @group repository
 * @group paciente
 */
class PacienteRepositoryTest extends TestCase
{
    private string $exercisePath;
    private string $dbPath;

    protected function setUp(): void
    {
        $this->exercisePath = __DIR__ . '/../../exercises/api-ejercicio';
        $this->dbPath = $this->exercisePath . '/database.sqlite';

        // Limpiar base de datos y resetear Singleton
        TestHelper::resetDatabaseSingleton();
        TestHelper::cleanDatabase($this->dbPath);
    }

    protected function tearDown(): void
    {
        TestHelper::resetDatabaseSingleton();
        TestHelper::cleanDatabase($this->dbPath);
    }

    public function testPacienteRepositoryFileExists(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $this->assertFileExists(
            $classFile,
            '❌ El archivo PacienteRepository.php debe existir en src/Repositorios/'
        );
    }

    public function testPacienteRepositoryUsesCorrectNamespace(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('PacienteRepository.php no existe aún');
        }

        $content = file_get_contents($classFile);

        $this->assertStringContainsString(
            'namespace ApiEjercicio\Repositorios',
            $content,
            '❌ PacienteRepository debe usar el namespace ApiEjercicio\Repositorios'
        );
    }

    public function testPacienteRepositoryHasRequiredMethods(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('PacienteRepository.php no existe aún');
        }

        require_once $this->exercisePath . '/src/Config/Database.php';
        require_once $classFile;

        $reflection = new \ReflectionClass('ApiEjercicio\Repositorios\PacienteRepository');

        $requiredMethods = ['obtenerTodos', 'obtenerPorId', 'crear', 'actualizar', 'eliminar'];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                $reflection->hasMethod($method),
                "❌ PacienteRepository debe tener el método {$method}()"
            );
        }
    }

    public function testPacienteRepositoryCrearMethod(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $classFile;

        // Inicializar base de datos
        \ApiEjercicio\Config\Database::getInstance();

        $repo = new \ApiEjercicio\Repositorios\PacienteRepository();

        $id = $repo->crear([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@test.com',
            'telefono' => '555-1234',
            'fecha_nacimiento' => '1990-01-01'
        ]);

        $this->assertIsInt($id, '❌ El método crear() debe retornar un entero (ID)');
        $this->assertGreaterThan(0, $id, '❌ El ID retornado debe ser mayor que 0');
    }

    public function testPacienteRepositoryObtenerTodosMethod(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $classFile;

        \ApiEjercicio\Config\Database::getInstance();
        $repo = new \ApiEjercicio\Repositorios\PacienteRepository();

        // Crear pacientes de prueba
        $repo->crear([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@test.com',
            'telefono' => '555-1234',
            'fecha_nacimiento' => '1990-01-01'
        ]);

        $repo->crear([
            'nombre' => 'María',
            'apellido' => 'García',
            'email' => 'maria@test.com',
            'telefono' => '555-5678',
            'fecha_nacimiento' => '1985-05-15'
        ]);

        $pacientes = $repo->obtenerTodos();

        $this->assertIsArray($pacientes, '❌ obtenerTodos() debe retornar un array');
        $this->assertCount(2, $pacientes, '❌ Debe haber 2 pacientes creados');
        $this->assertArrayHasKey('nombre', $pacientes[0], '❌ Cada paciente debe tener la key "nombre"');
        $this->assertArrayHasKey('email', $pacientes[0], '❌ Cada paciente debe tener la key "email"');
    }

    public function testPacienteRepositoryObtenerPorIdMethod(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $classFile;

        \ApiEjercicio\Config\Database::getInstance();
        $repo = new \ApiEjercicio\Repositorios\PacienteRepository();

        $id = $repo->crear([
            'nombre' => 'Test',
            'apellido' => 'Usuario',
            'email' => 'test@example.com',
            'telefono' => '555-0000',
            'fecha_nacimiento' => '1995-06-20'
        ]);

        $paciente = $repo->obtenerPorId($id);

        $this->assertIsArray($paciente, '❌ obtenerPorId() debe retornar un array');
        $this->assertEquals('Test', $paciente['nombre'], '❌ El nombre debe coincidir');
        $this->assertEquals('test@example.com', $paciente['email'], '❌ El email debe coincidir');
    }

    public function testPacienteRepositoryObtenerPorIdNotFound(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $classFile;

        \ApiEjercicio\Config\Database::getInstance();
        $repo = new \ApiEjercicio\Repositorios\PacienteRepository();

        $paciente = $repo->obtenerPorId(9999);

        $this->assertFalse(
            $paciente,
            '❌ obtenerPorId() debe retornar false cuando no existe el paciente'
        );
    }

    public function testPacienteRepositoryActualizarMethod(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $classFile;

        \ApiEjercicio\Config\Database::getInstance();
        $repo = new \ApiEjercicio\Repositorios\PacienteRepository();

        $id = $repo->crear([
            'nombre' => 'Original',
            'apellido' => 'Nombre',
            'email' => 'original@test.com',
            'telefono' => '555-1111',
            'fecha_nacimiento' => '1990-01-01'
        ]);

        $resultado = $repo->actualizar($id, [
            'nombre' => 'Actualizado',
            'apellido' => 'Apellido',
            'email' => 'actualizado@test.com',
            'telefono' => '555-2222',
            'fecha_nacimiento' => '1991-02-02'
        ]);

        $this->assertTrue($resultado, '❌ actualizar() debe retornar true cuando tiene éxito');

        $paciente = $repo->obtenerPorId($id);
        $this->assertEquals('Actualizado', $paciente['nombre'], '❌ El nombre debe estar actualizado');
        $this->assertEquals('actualizado@test.com', $paciente['email'], '❌ El email debe estar actualizado');
    }

    public function testPacienteRepositoryEliminarMethod(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $classFile;

        \ApiEjercicio\Config\Database::getInstance();
        $repo = new \ApiEjercicio\Repositorios\PacienteRepository();

        $id = $repo->crear([
            'nombre' => 'Borrar',
            'apellido' => 'Este',
            'email' => 'borrar@test.com',
            'telefono' => '555-9999',
            'fecha_nacimiento' => '1988-08-08'
        ]);

        $resultado = $repo->eliminar($id);

        $this->assertTrue($resultado, '❌ eliminar() debe retornar true cuando tiene éxito');

        $paciente = $repo->obtenerPorId($id);
        $this->assertFalse($paciente, '❌ El paciente eliminado no debe poder ser encontrado');
    }

    public function testPacienteRepositoryHandlesOptionalFields(): void
    {
        $classFile = $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aún');
        }

        require_once $dbFile;
        require_once $classFile;

        \ApiEjercicio\Config\Database::getInstance();
        $repo = new \ApiEjercicio\Repositorios\PacienteRepository();

        // Crear con solo campos obligatorios
        $id = $repo->crear([
            'nombre' => 'Solo',
            'apellido' => 'Obligatorios',
            'email' => 'solo@test.com'
        ]);

        $this->assertIsInt($id, '❌ Debe poder crear con solo campos obligatorios');
        $this->assertGreaterThan(0, $id);

        $paciente = $repo->obtenerPorId($id);
        $this->assertEquals('Solo', $paciente['nombre']);
        $this->assertNull($paciente['telefono'] ?? null, '❌ Campos opcionales deben ser null');
    }
}
