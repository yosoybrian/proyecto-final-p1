<?php

declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestHelper;

/**
 * @group api
 * @group database
 */
class DatabaseTest extends TestCase
{
    private string $exercisePath;
    private string $dbPath;

    protected function setUp(): void
    {
        $this->exercisePath = __DIR__ . '/../../exercises/api-ejercicio';
        $this->dbPath = $this->exercisePath . '/database.sqlite';

        // Limpiar base de datos antes de cada test
        TestHelper::resetDatabaseSingleton();
        TestHelper::cleanDatabase($this->dbPath);
    }

    protected function tearDown(): void
    {
        // Limpiar después de los tests
        TestHelper::resetDatabaseSingleton();
        TestHelper::cleanDatabase($this->dbPath);
    }    public function testDatabaseFileExists(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';
        $this->assertFileExists(
            $classFile,
            '❌ El archivo Database.php debe existir en exercises/api-ejercicio/src/Config/'
        );
    }

    public function testDatabaseClassCanBeLoaded(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        $this->assertTrue(
            class_exists('ApiEjercicio\Config\Database', false),
            '❌ La clase ApiEjercicio\Config\Database debe existir'
        );
    }

    public function testDatabaseUsesCorrectNamespace(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        $content = file_get_contents($classFile);

        $this->assertStringContainsString(
            'namespace ApiEjercicio\Config',
            $content,
            '❌ Database.php debe usar el namespace ApiEjercicio\Config'
        );
    }

    public function testDatabaseImplementsSingleton(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        $reflection = new \ReflectionClass('ApiEjercicio\Config\Database');

        // Verificar que tiene método getInstance
        $this->assertTrue(
            $reflection->hasMethod('getInstance'),
            '❌ Database debe tener el método getInstance()'
        );

        // Verificar que getInstance es estático
        $method = $reflection->getMethod('getInstance');
        $this->assertTrue(
            $method->isStatic(),
            '❌ getInstance() debe ser un método estático'
        );

        // Verificar que el constructor es privado
        $constructor = $reflection->getConstructor();
        $this->assertTrue(
            $constructor->isPrivate(),
            '❌ El constructor de Database debe ser privado (patrón Singleton)'
        );
    }

    public function testDatabaseCreatesConnection(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        $db = \ApiEjercicio\Config\Database::getInstance();

        $this->assertInstanceOf(
            \ApiEjercicio\Config\Database::class,
            $db,
            '❌ getInstance() debe retornar una instancia de Database'
        );
    }

    public function testDatabaseReturnsConnection(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        $db = \ApiEjercicio\Config\Database::getInstance();
        $conexion = $db->getConexion();

        $this->assertInstanceOf(
            \PDO::class,
            $conexion,
            '❌ getConexion() debe retornar una instancia de PDO'
        );
    }

    public function testDatabaseCreatesPacientesTable(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        $db = \ApiEjercicio\Config\Database::getInstance();
        $conexion = $db->getConexion();

        // Verificar que la tabla pacientes existe
        $result = $conexion->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='pacientes'"
        );

        $this->assertNotFalse(
            $result->fetch(),
            '❌ La tabla "pacientes" debe ser creada automáticamente'
        );
    }

    public function testDatabaseCreatesCitasTable(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        $db = \ApiEjercicio\Config\Database::getInstance();
        $conexion = $db->getConexion();

        // Verificar que la tabla citas existe
        $result = $conexion->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='citas'"
        );

        $this->assertNotFalse(
            $result->fetch(),
            '❌ La tabla "citas" debe ser creada automáticamente'
        );
    }

    public function testPacientesTableHasCorrectStructure(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        $db = \ApiEjercicio\Config\Database::getInstance();
        $conexion = $db->getConexion();

        // Obtener estructura de la tabla
        $result = $conexion->query("PRAGMA table_info(pacientes)");
        $columns = $result->fetchAll(\PDO::FETCH_ASSOC);

        $columnNames = array_column($columns, 'name');

        $requiredColumns = ['id', 'nombre', 'apellido', 'email', 'telefono', 'fecha_nacimiento'];

        foreach ($requiredColumns as $column) {
            $this->assertContains(
                $column,
                $columnNames,
                "❌ La tabla pacientes debe tener la columna '$column'"
            );
        }
    }

    public function testCitasTableHasCorrectStructure(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        $db = \ApiEjercicio\Config\Database::getInstance();
        $conexion = $db->getConexion();

        // Obtener estructura de la tabla
        $result = $conexion->query("PRAGMA table_info(citas)");
        $columns = $result->fetchAll(\PDO::FETCH_ASSOC);

        $columnNames = array_column($columns, 'name');

        $requiredColumns = ['id', 'paciente_id', 'fecha_hora', 'motivo', 'estado'];

        foreach ($requiredColumns as $column) {
            $this->assertContains(
                $column,
                $columnNames,
                "❌ La tabla citas debe tener la columna '$column'"
            );
        }
    }

    public function testSingletonReturnsAlwaysSameInstance(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        $db1 = \ApiEjercicio\Config\Database::getInstance();
        $db2 = \ApiEjercicio\Config\Database::getInstance();

        $this->assertSame(
            $db1,
            $db2,
            '❌ getInstance() debe retornar SIEMPRE la misma instancia (Singleton)'
        );
    }

    public function testDatabaseFileIsCreated(): void
    {
        $classFile = $this->exercisePath . '/src/Config/Database.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('Database.php no existe aún');
        }

        require_once $classFile;

        // Forzar nueva instancia usando reflection
        $reflection = new \ReflectionClass('ApiEjercicio\Config\Database');
        $instance = $reflection->getProperty('instancia');
        $instance->setAccessible(true);
        $instance->setValue(null, null);

        \ApiEjercicio\Config\Database::getInstance();

        $this->assertFileExists(
            $this->dbPath,
            '❌ El archivo database.sqlite debe ser creado automáticamente'
        );
    }
}
