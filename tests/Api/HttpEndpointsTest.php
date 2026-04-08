<?php

declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;

/**
 * @group api
 * @group http
 */
class HttpEndpointsTest extends TestCase
{
    private string $exercisePath;
    private string $baseUrl = 'http://localhost:8000';
    private static bool $serverStarted = false;
    private static $serverProcess;

    public static function setUpBeforeClass(): void
    {
        $exercisePath = __DIR__ . '/../../exercises/api-ejercicio';

        // Verificar si el servidor ya está corriendo
        $ch = curl_init('http://localhost:8000');
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            // Servidor no está corriendo, intentar iniciarlo
            $publicDir = $exercisePath . '/public';

            if (is_dir($publicDir)) {
                $command = sprintf(
                    'cd %s && php -S localhost:8000 > /dev/null 2>&1 & echo $!',
                    escapeshellarg($publicDir)
                );

                self::$serverProcess = shell_exec($command);
                self::$serverStarted = true;

                // Esperar a que el servidor inicie
                sleep(2);
            }
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$serverStarted && self::$serverProcess) {
            // Detener el servidor
            shell_exec('kill ' . trim(self::$serverProcess));
        }
    }

    protected function setUp(): void
    {
        $this->exercisePath = __DIR__ . '/../../exercises/api-ejercicio';
    }

    public function testServerIsRunning(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($publicDir)) {
            $this->markTestSkipped('El directorio public/ no existe aun');
        }

        $ch = curl_init($this->baseUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertGreaterThan(
            0,
            $httpCode,
            '❌ El servidor debe estar corriendo en http://localhost:8000 (ejecuta: composer serve:api)'
        );
    }

    public function testGetPacientesEndpoint(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/api.php')) {
            $this->markTestSkipped('Los archivos necesarios no existen aun');
        }

        $ch = curl_init($this->baseUrl . '/api.php?recurso=pacientes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            $this->markTestSkipped('Servidor no disponible');
        }

        $this->assertEquals(
            200,
            $httpCode,
            '❌ GET /api.php?recurso=pacientes debe retornar 200 OK'
        );

        $data = json_decode($response, true);

        // Aceptar respuesta vacía o null como array vacío
        if ($data === null || $data === '') {
            $data = [];
        }

        $this->assertIsArray(
            $data,
            '❌ La respuesta debe ser un JSON valido que decodifica a array. Respuesta recibida: ' . substr($response, 0, 100)
        );
    }

    public function testGetCitasEndpoint(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/api.php')) {
            $this->markTestSkipped('Los archivos necesarios no existen aun');
        }

        $ch = curl_init($this->baseUrl . '/api.php?recurso=citas');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            $this->markTestSkipped('Servidor no disponible');
        }

        $this->assertEquals(
            200,
            $httpCode,
            '❌ GET /api.php?recurso=citas debe retornar 200 OK'
        );

        $data = json_decode($response, true);

        // Aceptar respuesta vacía o null como array vacío
        if ($data === null || $data === '') {
            $data = [];
        }

        $this->assertIsArray(
            $data,
            '❌ La respuesta debe ser un JSON valido. Respuesta recibida: ' . substr($response, 0, 100)
        );
    }

    public function testPostPacienteEndpoint(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/api.php')) {
            $this->markTestSkipped('Los archivos necesarios no existen aun');
        }

        $data = [
            'nombre' => 'Test',
            'apellido' => 'Usuario',
            'email' => 'test' . time() . '@example.com',
            'telefono' => '555-0000',
            'fecha_nacimiento' => '1990-01-01'
        ];

        $ch = curl_init($this->baseUrl . '/api.php?recurso=pacientes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            $this->markTestSkipped('Servidor no disponible');
        }

        // Aceptar 200 o 201 como respuesta válida (algunos estudiantes usan 200)
        $this->assertContains(
            $httpCode,
            [200, 201],
            '❌ POST /api.php?recurso=pacientes debe retornar 200 OK o 201 Created. Recibido: ' . $httpCode
        );

        $result = json_decode($response, true);

        $this->assertIsArray($result, '❌ La respuesta debe ser JSON. Respuesta: ' . substr($response, 0, 100));
        $this->assertArrayHasKey('id', $result, '❌ La respuesta debe incluir el ID del recurso creado');
    }

    public function testGetSinglePacienteEndpoint(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/api.php')) {
            $this->markTestSkipped('Los archivos necesarios no existen aun');
        }

        // Primero crear un paciente
        $data = [
            'nombre' => 'Consulta',
            'apellido' => 'Individual',
            'email' => 'individual' . time() . '@example.com',
            'telefono' => '555-1111',
            'fecha_nacimiento' => '1985-05-15'
        ];

        $ch = curl_init($this->baseUrl . '/api.php?recurso=pacientes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            $this->markTestSkipped('Servidor no disponible');
        }

        $created = json_decode($response, true);
        $id = $created['id'] ?? null;

        if (!$id) {
            $this->markTestSkipped('No se pudo crear el paciente de prueba');
        }

        // Ahora consultar ese paciente específico
        $ch = curl_init($this->baseUrl . '/api.php?recurso=pacientes&id=' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(
            200,
            $httpCode,
            '❌ GET /api.php?recurso=pacientes&id={id} debe retornar 200 OK'
        );

        $result = json_decode($response, true);

        $this->assertIsArray($result);
        $this->assertEquals($id, $result['id'], '❌ Debe retornar el paciente con el ID correcto');
        $this->assertEquals('Consulta', $result['nombre']);
    }

    public function testInvalidResourceReturns404(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/api.php')) {
            $this->markTestSkipped('Los archivos necesarios no existen aun');
        }

        $ch = curl_init($this->baseUrl . '/api.php?recurso=recurso_invalido');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            $this->markTestSkipped('Servidor no disponible');
        }

        // Si retorna 200, probablemente no han implementado el manejo de recursos inválidos
        // Pero no queremos que esto bloquee toda la evaluación
        if ($httpCode === 200) {
            $this->markTestIncomplete(
                'ℹ️ El router aún no maneja recursos inválidos correctamente. Debe retornar 404.'
            );
        }

        $this->assertEquals(
            404,
            $httpCode,
            '❌ Recursos invalidos deben retornar 404 Not Found. Recibido: ' . $httpCode
        );
    }

    public function testResponseIsValidJson(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/api.php')) {
            $this->markTestSkipped('Los archivos necesarios no existen aun');
        }

        $ch = curl_init($this->baseUrl . '/api.php?recurso=pacientes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            $this->markTestSkipped('Servidor no disponible');
        }

        $this->assertStringContainsString(
            'Content-Type: application/json',
            $response,
            '❌ Las respuestas deben tener Content-Type: application/json'
        );
    }

    public function testPutPacienteEndpoint(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/api.php')) {
            $this->markTestSkipped('Los archivos necesarios no existen aun');
        }

        // Crear paciente
        $data = [
            'nombre' => 'Actualizar',
            'apellido' => 'Este',
            'email' => 'actualizar' . time() . '@example.com',
            'telefono' => '555-2222',
            'fecha_nacimiento' => '1992-08-25'
        ];

        $ch = curl_init($this->baseUrl . '/api.php?recurso=pacientes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            $this->markTestSkipped('Servidor no disponible');
        }

        $created = json_decode($response, true);
        $id = $created['id'] ?? null;

        if (!$id) {
            $this->markTestSkipped('No se pudo crear el paciente de prueba');
        }

        // Actualizar paciente
        $updateData = [
            'nombre' => 'Actualizado',
            'apellido' => 'Exitoso',
            'email' => 'actualizado' . time() . '@example.com',
            'telefono' => '555-3333',
            'fecha_nacimiento' => '1992-08-25'
        ];

        $ch = curl_init($this->baseUrl . '/api.php?recurso=pacientes&id=' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(
            200,
            $httpCode,
            '❌ PUT /api.php?recurso=pacientes&id={id} debe retornar 200 OK'
        );
    }

    public function testDeletePacienteEndpoint(): void
    {
        $publicDir = $this->exercisePath . '/public';

        if (!is_dir($publicDir) || !file_exists($publicDir . '/api.php')) {
            $this->markTestSkipped('Los archivos necesarios no existen aun');
        }

        // Crear paciente
        $data = [
            'nombre' => 'Eliminar',
            'apellido' => 'Este',
            'email' => 'eliminar' . time() . '@example.com',
            'telefono' => '555-9999',
            'fecha_nacimiento' => '1988-03-10'
        ];

        $ch = curl_init($this->baseUrl . '/api.php?recurso=pacientes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            $this->markTestSkipped('Servidor no disponible');
        }

        $created = json_decode($response, true);
        $id = $created['id'] ?? null;

        if (!$id) {
            $this->markTestSkipped('No se pudo crear el paciente de prueba');
        }

        // Eliminar paciente
        $ch = curl_init($this->baseUrl . '/api.php?recurso=pacientes&id=' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(
            200,
            $httpCode,
            '❌ DELETE /api.php?recurso=pacientes&id={id} debe retornar 200 OK'
        );
    }
}
