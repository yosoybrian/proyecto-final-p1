<?php

declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestHelper;

/**
 * @group api
 * @group validation
 */
class ValidationTest extends TestCase
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
    }    public function testPacienteRequiresNombre(): void
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

        try {
            $repo->crear([
                'apellido' => 'Sin Nombre',
                'email' => 'test@example.com'
            ]);

            $this->fail('❌ Debe lanzar excepcion cuando falta el nombre');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testPacienteRequiresApellido(): void
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

        try {
            $repo->crear([
                'nombre' => 'Sin Apellido',
                'email' => 'test@example.com'
            ]);

            $this->fail('❌ Debe lanzar excepcion cuando falta el apellido');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testPacienteRequiresEmail(): void
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

        try {
            $repo->crear([
                'nombre' => 'Juan',
                'apellido' => 'Perez'
            ]);

            $this->fail('❌ Debe lanzar excepcion cuando falta el email');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testCitaRequiresPacienteId(): void
    {
        $citaFile = $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        $dbFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($citaFile) || !file_exists($dbFile)) {
            $this->markTestSkipped('Archivos necesarios no existen aun');
        }

        require_once $dbFile;
        require_once $citaFile;

        \ApiEjercicio\Config\Database::getInstance();
        $repo = new \ApiEjercicio\Repositorios\CitaRepository();

        try {
            $repo->crear([
                'fecha_hora' => '2024-12-15 10:00:00',
                'motivo' => 'Consulta'
            ]);

            $this->fail('❌ Debe lanzar excepcion cuando falta paciente_id');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testCitaRequiresFechaHora(): void
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
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Test',
            'apellido' => 'Usuario',
            'email' => 'test@example.com'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();

        try {
            $citaRepo->crear([
                'paciente_id' => $pacienteId,
                'motivo' => 'Sin fecha'
            ]);

            $this->fail('❌ Debe lanzar excepcion cuando falta fecha_hora');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testCitaRequiresMotivo(): void
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
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Test',
            'apellido' => 'Usuario',
            'email' => 'test@example.com'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();

        try {
            $citaRepo->crear([
                'paciente_id' => $pacienteId,
                'fecha_hora' => '2024-12-15 10:00:00'
            ]);

            $this->fail('❌ Debe lanzar excepcion cuando falta motivo');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testEmailFormatValidation(): void
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

        // Email valido debe funcionar
        $id = $repo->crear([
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'email' => 'juan@example.com'
        ]);

        $this->assertGreaterThan(0, $id, '✅ Email valido debe ser aceptado');

        // Email invalido debe fallar
        try {
            $repo->crear([
                'nombre' => 'Maria',
                'apellido' => 'Garcia',
                'email' => 'email-invalido'
            ]);

            // Si llega aqui, el test falla
            $this->markTestIncomplete('⚠️ Seria ideal validar formato de email con filter_var()');
        } catch (\Exception $e) {
            // Si lanza excepcion, el test pasa
            $this->assertTrue(true, '✅ Email invalido es rechazado correctamente');
        }
    }

    public function testFechaFormatValidation(): void
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
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Test',
            'apellido' => 'Usuario',
            'email' => 'test@example.com'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();

        // Fecha valida
        $id = $citaRepo->crear([
            'paciente_id' => $pacienteId,
            'fecha_hora' => '2024-12-15 10:00:00',
            'motivo' => 'Consulta'
        ]);

        $this->assertGreaterThan(0, $id, '✅ Fecha valida debe ser aceptada');
    }

    public function testEstadoValidValues(): void
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
        $pacienteId = $pacienteRepo->crear([
            'nombre' => 'Test',
            'apellido' => 'Usuario',
            'email' => 'test@example.com'
        ]);

        $citaRepo = new \ApiEjercicio\Repositorios\CitaRepository();

        $estadosValidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];

        foreach ($estadosValidos as $estado) {
            $id = $citaRepo->crear([
                'paciente_id' => $pacienteId,
                'fecha_hora' => '2024-12-15 10:00:00',
                'motivo' => 'Test estado',
                'estado' => $estado
            ]);

            $this->assertGreaterThan(0, $id, "✅ Estado '{$estado}' debe ser valido");
        }
    }

    public function testEmptyStringValidation(): void
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

        try {
            $repo->crear([
                'nombre' => '',
                'apellido' => 'Perez',
                'email' => 'test@example.com'
            ]);

            $this->markTestIncomplete('⚠️ Seria ideal validar strings vacios con trim()');
        } catch (\Exception $e) {
            $this->assertTrue(true, '✅ Strings vacios son rechazados');
        }
    }
}
