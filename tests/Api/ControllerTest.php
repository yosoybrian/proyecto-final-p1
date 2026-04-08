<?php

declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;

/**
 * @group api
 * @group controller
 */
class ControllerTest extends TestCase
{
    private string $exercisePath;

    protected function setUp(): void
    {
        $this->exercisePath = __DIR__ . '/../../exercises/api-ejercicio';
    }

    public function testPacienteControllerFileExists(): void
    {
        $classFile = $this->exercisePath . '/src/Controladores/PacienteController.php';
        $this->assertFileExists(
            $classFile,
            '❌ El archivo PacienteController.php debe existir en src/Controladores/'
        );
    }

    public function testCitaControllerFileExists(): void
    {
        $classFile = $this->exercisePath . '/src/Controladores/CitaController.php';
        $this->assertFileExists(
            $classFile,
            '❌ El archivo CitaController.php debe existir en src/Controladores/'
        );
    }

    public function testPacienteControllerUsesCorrectNamespace(): void
    {
        $classFile = $this->exercisePath . '/src/Controladores/PacienteController.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('PacienteController.php no existe aun');
        }

        $content = file_get_contents($classFile);

        $this->assertStringContainsString(
            'namespace ApiEjercicio\Controladores',
            $content,
            '❌ PacienteController debe usar el namespace ApiEjercicio\Controladores'
        );
    }

    public function testCitaControllerUsesCorrectNamespace(): void
    {
        $classFile = $this->exercisePath . '/src/Controladores/CitaController.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('CitaController.php no existe aun');
        }

        $content = file_get_contents($classFile);

        $this->assertStringContainsString(
            'namespace ApiEjercicio\Controladores',
            $content,
            '❌ CitaController debe usar el namespace ApiEjercicio\Controladores'
        );
    }

    public function testPacienteControllerHasRequiredMethods(): void
    {
        $classFile = $this->exercisePath . '/src/Controladores/PacienteController.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('PacienteController.php no existe aun');
        }

        require_once $this->exercisePath . '/src/Config/Database.php';
        require_once $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        require_once $classFile;

        $reflection = new \ReflectionClass('ApiEjercicio\Controladores\PacienteController');

        $requiredMethods = ['index', 'show', 'store', 'update', 'destroy'];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                $reflection->hasMethod($method),
                "❌ PacienteController debe tener el metodo {$method}()"
            );
        }
    }

    public function testCitaControllerHasRequiredMethods(): void
    {
        $classFile = $this->exercisePath . '/src/Controladores/CitaController.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('CitaController.php no existe aun');
        }

        require_once $this->exercisePath . '/src/Config/Database.php';
        require_once $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        require_once $this->exercisePath . '/src/Repositorios/CitaRepository.php';
        require_once $classFile;

        $reflection = new \ReflectionClass('ApiEjercicio\Controladores\CitaController');

        $requiredMethods = ['index', 'show', 'store', 'update', 'destroy'];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                $reflection->hasMethod($method),
                "❌ CitaController debe tener el metodo {$method}()"
            );
        }
    }

    public function testRouterFileExists(): void
    {
        $routerFile = $this->exercisePath . '/router.php';
        $this->assertFileExists(
            $routerFile,
            '❌ El archivo router.php debe existir en la raiz del ejercicio'
        );
    }

    public function testPublicApiFileExists(): void
    {
        $apiFile = $this->exercisePath . '/public/api.php';
        $this->assertFileExists(
            $apiFile,
            '❌ El archivo public/api.php debe existir'
        );
    }

    public function testPublicIndexFileExists(): void
    {
        $indexFile = $this->exercisePath . '/public/index.php';
        $this->assertFileExists(
            $indexFile,
            '❌ El archivo public/index.php debe existir'
        );
    }

    public function testRouterHandlesHttpMethods(): void
    {
        $routerFile = $this->exercisePath . '/router.php';

        if (!file_exists($routerFile)) {
            $this->markTestSkipped('router.php no existe aun');
        }

        $content = file_get_contents($routerFile);

        $this->assertStringContainsString(
            '$_SERVER[\'REQUEST_METHOD\']',
            $content,
            '❌ router.php debe verificar REQUEST_METHOD'
        );
    }

    public function testRouterHandlesGet(): void
    {
        $routerFile = $this->exercisePath . '/router.php';

        if (!file_exists($routerFile)) {
            $this->markTestSkipped('router.php no existe aun');
        }

        $content = file_get_contents($routerFile);

        // Verificar que contiene case 'GET' y llamada a index()
        $hasGet = preg_match("/case\s+['\"]GET['\"]/i", $content);
        $hasIndex = preg_match('/->\s*index\s*\(/i', $content);

        $this->assertTrue(
            $hasGet && $hasIndex,
            '❌ router.php debe manejar GET requests llamando a index()'
        );
    }

    public function testRouterHandlesPost(): void
    {
        $routerFile = $this->exercisePath . '/router.php';

        if (!file_exists($routerFile)) {
            $this->markTestSkipped('router.php no existe aun');
        }

        $content = file_get_contents($routerFile);

        // Verificar que contiene case 'POST' y llamada a store()
        $hasPost = preg_match("/case\s+['\"]POST['\"]/i", $content);
        $hasStore = preg_match('/->\s*store\s*\(/i', $content);

        $this->assertTrue(
            $hasPost && $hasStore,
            '❌ router.php debe manejar POST requests llamando a store()'
        );
    }

    public function testRouterHandlesPut(): void
    {
        $routerFile = $this->exercisePath . '/router.php';

        if (!file_exists($routerFile)) {
            $this->markTestSkipped('router.php no existe aun');
        }

        $content = file_get_contents($routerFile);

        // Verificar que contiene case 'PUT' y llamada a update()
        $hasPut = preg_match("/case\s+['\"]PUT['\"]/i", $content);
        $hasUpdate = preg_match('/->\s*update\s*\(/i', $content);

        $this->assertTrue(
            $hasPut && $hasUpdate,
            '❌ router.php debe manejar PUT requests llamando a update()'
        );
    }

    public function testRouterHandlesDelete(): void
    {
        $routerFile = $this->exercisePath . '/router.php';

        if (!file_exists($routerFile)) {
            $this->markTestSkipped('router.php no existe aun');
        }

        $content = file_get_contents($routerFile);

        // Verificar que contiene case 'DELETE' y llamada a destroy()
        $hasDelete = preg_match("/case\s+['\"]DELETE['\"]/i", $content);
        $hasDestroy = preg_match('/->\s*destroy\s*\(/i', $content);

        $this->assertTrue(
            $hasDelete && $hasDestroy,
            '❌ router.php debe manejar DELETE requests llamando a destroy()'
        );
    }

    public function testApiPhpIncludesRouter(): void
    {
        $apiFile = $this->exercisePath . '/public/api.php';

        if (!file_exists($apiFile)) {
            $this->markTestSkipped('api.php no existe aun');
        }

        $content = file_get_contents($apiFile);

        $this->assertMatchesRegularExpression(
            '/require|include.*router\.php/i',
            $content,
            '❌ api.php debe incluir router.php'
        );
    }

    public function testApiPhpSetsJsonHeader(): void
    {
        $apiFile = $this->exercisePath . '/public/api.php';

        if (!file_exists($apiFile)) {
            $this->markTestSkipped('api.php no existe aun');
        }

        $content = file_get_contents($apiFile);

        $this->assertStringContainsString(
            'application/json',
            $content,
            '❌ api.php debe establecer Content-Type: application/json'
        );
    }

    public function testControllersReturnJsonResponses(): void
    {
        $classFile = $this->exercisePath . '/src/Controladores/PacienteController.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('PacienteController.php no existe aun');
        }

        $content = file_get_contents($classFile);

        $this->assertStringContainsString(
            'json_encode',
            $content,
            '❌ Los controladores deben usar json_encode para las respuestas'
        );
    }

    public function testIndexMethodReturnsVoid(): void
    {
        $classFile = $this->exercisePath . '/src/Controladores/PacienteController.php';

        if (!file_exists($classFile)) {
            $this->markTestSkipped('PacienteController.php no existe aun');
        }

        require_once $this->exercisePath . '/src/Config/Database.php';
        require_once $this->exercisePath . '/src/Repositorios/PacienteRepository.php';
        require_once $classFile;

        $reflection = new \ReflectionClass('ApiEjercicio\Controladores\PacienteController');
        $method = $reflection->getMethod('index');

        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType, '❌ index() debe declarar tipo de retorno');
        $this->assertEquals('void', $returnType->getName(), '❌ index() debe retornar void');
    }
}
