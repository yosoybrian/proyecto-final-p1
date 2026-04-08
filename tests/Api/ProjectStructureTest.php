<?php

declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;

/**
 * @group api
 * @group structure
 */
class ProjectStructureTest extends TestCase
{
    private string $exercisePath;

    protected function setUp(): void
    {
        $this->exercisePath = __DIR__ . '/../../exercises/api-ejercicio';
    }

    public function testExerciseDirectoryExists(): void
    {
        $this->assertDirectoryExists(
            $this->exercisePath,
            '❌ El directorio exercises/api-ejercicio/ debe existir'
        );
    }

    public function testSrcDirectoryExists(): void
    {
        $srcDir = $this->exercisePath . '/src';

        if (!is_dir($this->exercisePath)) {
            $this->markTestSkipped('Directorio de ejercicio no existe aun');
        }

        $this->assertDirectoryExists(
            $srcDir,
            '❌ El directorio src/ debe existir en exercises/api-ejercicio/'
        );
    }

    public function testConfigDirectoryExists(): void
    {
        $configDir = $this->exercisePath . '/src/Config';

        if (!is_dir($this->exercisePath . '/src')) {
            $this->markTestSkipped('Directorio src/ no existe aun');
        }

        $this->assertDirectoryExists(
            $configDir,
            '❌ El directorio src/Config/ debe existir'
        );
    }

    public function testRepositoriosDirectoryExists(): void
    {
        $repoDir = $this->exercisePath . '/src/Repositorios';

        if (!is_dir($this->exercisePath . '/src')) {
            $this->markTestSkipped('Directorio src/ no existe aun');
        }

        $this->assertDirectoryExists(
            $repoDir,
            '❌ El directorio src/Repositorios/ debe existir'
        );
    }

    public function testControladoresDirectoryExists(): void
    {
        $controllerDir = $this->exercisePath . '/src/Controladores';

        if (!is_dir($this->exercisePath . '/src')) {
            $this->markTestSkipped('Directorio src/ no existe aun');
        }

        $this->assertDirectoryExists(
            $controllerDir,
            '❌ El directorio src/Controladores/ debe existir'
        );
    }

    public function testPublicDirectoryExists(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($this->exercisePath)) {
            $this->markTestSkipped('Directorio de ejercicio no existe aun');
        }

        $this->assertDirectoryExists(
            $publicDir,
            '❌ El directorio public/ debe existir en exercises/api-ejercicio/'
        );
    }

    public function testAllRequiredFilesExist(): void
    {
        if (!is_dir($this->exercisePath)) {
            $this->markTestSkipped('Directorio de ejercicio no existe aun');
        }

        $requiredFiles = [
            '/src/Config/Database.php',
            '/src/Repositorios/PacienteRepository.php',
            '/src/Repositorios/CitaRepository.php',
            '/src/Controladores/PacienteController.php',
            '/src/Controladores/CitaController.php',
            '/router.php',
            '/public/api.php',
            '/public/index.php'
        ];

        $missingFiles = [];

        foreach ($requiredFiles as $file) {
            if (!file_exists($this->exercisePath . $file)) {
                $missingFiles[] = $file;
            }
        }

        if (!empty($missingFiles)) {
            $this->markTestIncomplete(
                "⚠️ Archivos faltantes:\n" . implode("\n", $missingFiles)
            );
        }

        $this->assertEmpty($missingFiles, '✅ Todos los archivos requeridos existen');
    }

    public function testAutoloadConfigurationIsCorrect(): void
    {
        $composerFile = __DIR__ . '/../../composer.json';
        $content = json_decode(file_get_contents($composerFile), true);

        $this->assertArrayHasKey('autoload', $content);
        $this->assertArrayHasKey('psr-4', $content['autoload']);
        $this->assertArrayHasKey('ApiEjercicio\\', $content['autoload']['psr-4']);

        $this->assertEquals(
            'exercises/api-ejercicio/src/',
            $content['autoload']['psr-4']['ApiEjercicio\\'],
            '✅ Autoload configurado correctamente en composer.json'
        );
    }

    public function testComposerScriptsExist(): void
    {
        $composerFile = __DIR__ . '/../../composer.json';
        $content = json_decode(file_get_contents($composerFile), true);

        $this->assertArrayHasKey('scripts', $content);
        $this->assertArrayHasKey('test:api', $content['scripts']);
        $this->assertArrayHasKey('serve:api', $content['scripts']);

        $this->assertTrue(true, '✅ Scripts de composer configurados');
    }

    public function testExerciseDocumentationExists(): void
    {
        $ejercicioFile = __DIR__ . '/../../tasks/ejercicio-api-rest.md';

        $this->assertFileExists(
            $ejercicioFile,
            '❌ El archivo tasks/ejercicio-api-rest.md debe existir'
        );

        $content = file_get_contents($ejercicioFile);

        $this->assertStringContainsString('API REST', $content);
        $this->assertStringContainsString('Database', $content);
        $this->assertStringContainsString('Repository', $content);
        $this->assertStringContainsString('Controller', $content);

        $this->assertTrue(true, '✅ Documentacion del ejercicio existe y esta completa');
    }
}
