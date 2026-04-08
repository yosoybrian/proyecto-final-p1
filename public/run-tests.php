<?php
// Cambiar al directorio del proyecto (dinámico)
$projectRoot = dirname(__DIR__);
chdir($projectRoot);

// Ejecutar PHPUnit y capturar la salida
ob_start();
$output = shell_exec('vendor/bin/phpunit --testdox --colors=never 2>&1');
ob_end_clean();

// Leer también el archivo HTML de resultados si existe
$htmlResults = '';
$htmlResultsPath = $projectRoot . '/test-results.html';
if (file_exists($htmlResultsPath)) {
    $htmlResults = file_get_contents($htmlResultsPath);
}

echo "<pre>" . htmlspecialchars($output) . "</pre>";

if ($htmlResults) {
    echo "<h3>Resultados Detallados</h3>";
    echo $htmlResults;
}
?>
