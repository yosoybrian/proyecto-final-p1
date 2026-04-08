<?php
// Cambiar al directorio del proyecto
$projectDir = dirname(__DIR__); // Usar directorio relativo para mayor flexibilidad
chdir($projectDir);

// Definir rutas de scripts posibles (en orden de preferencia)
$possibleScripts = [
    $projectDir . '/scripts/secure-grading-report.sh',
    $projectDir . '/scripts/generate-grading-report.sh'
];

$scriptToUse = null;
$scriptName = '';

// Encontrar el primer script que existe
foreach ($possibleScripts as $script) {
    if (file_exists($script)) {
        $scriptToUse = $script;
        $scriptName = basename($script);
        break;
    }
}

// Si no se encuentra ningún script, usar composer como fallback
if (!$scriptToUse) {
    ob_start();
    $output = shell_exec('composer autograding 2>&1');
    ob_end_clean();
    $scriptName = 'composer autograding';
} else {
    // Verificar si el script ya es ejecutable
    $isExecutable = is_executable($scriptToUse);

    // Solo intentar chmod si no es ejecutable
    if (!$isExecutable) {
        // Suprimir errores de chmod y usar @ para evitar warnings
        $chmodSuccess = @chmod($scriptToUse, 0755);
        $isExecutable = is_executable($scriptToUse);
    } else {
        $chmodSuccess = true; // Ya es ejecutable, no necesita chmod
    }

    // Si el archivo es ejecutable (con o sin chmod), ejecutarlo
    if ($isExecutable) {
        // Ejecutar el script encontrado
        ob_start();
        $output = shell_exec('./' . str_replace($projectDir . '/', '', $scriptToUse) . ' 2>&1');
        ob_end_clean();
    } else {
        // Si falla todo, usar composer como fallback
        ob_start();
        $output = shell_exec('composer autograding 2>&1');
        ob_end_clean();
        $scriptName = 'composer autograding (fallback - chmod failed)';
    }
}

// Procesar la salida para mostrar mejor en HTML
$lines = explode("\n", $output);
$htmlOutput = '';
$inSummary = false;
$score = '';
$percentage = '';
$testResults = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;

    // Extraer puntuación (varios formatos posibles)
    if (strpos($line, 'Puntuación:') !== false || strpos($line, 'Puntuación') !== false) {
        if (preg_match('/(\d+(?:\.\d+)?)\/(\d+(?:\.\d+)?) puntos? \((\d+(?:\.\d+)?)%\)/', $line, $matches)) {
            $score = $matches[1] . ' / ' . $matches[2];
            $percentage = $matches[3];
        } elseif (preg_match('/(\d+(?:\.\d+)?)\/(\d+(?:\.\d+)?) \((\d+(?:\.\d+)?)%\)/', $line, $matches)) {
            $score = $matches[1] . ' / ' . $matches[2];
            $percentage = $matches[3];
        }
    }

    // Formatear líneas para HTML (múltiples formatos)
    if (strpos($line, '[SUCCESS]') !== false && strpos($line, 'PASSED:') !== false) {
        // Formato del script seguro: [SUCCESS] PASSED: Test Name (+X pts)
        if (preg_match('/PASSED:\s*(.+?)\s*\(\+[\d.]+\s*pts?\)/', $line, $matches)) {
            $testName = $matches[1];
            $htmlOutput .= '<div class="test-result passed">✅ ' . htmlspecialchars($testName) . '</div>';
            $testResults[] = ['name' => $testName, 'status' => 'passed'];
        }
    } elseif (strpos($line, '[ERROR]') !== false && strpos($line, 'FAILED:') !== false) {
        // Formato del script seguro: [ERROR] FAILED: Test Name (0 pts)
        if (preg_match('/FAILED:\s*(.+?)\s*\([\d.]+\s*pts?\)/', $line, $matches)) {
            $testName = $matches[1];
            $htmlOutput .= '<div class="test-result failed">❌ ' . htmlspecialchars($testName) . '</div>';
            $testResults[] = ['name' => $testName, 'status' => 'failed'];
        }
    } elseif (strpos($line, '✅ PASSED:') === 0) {
        // Formato clásico
        $testName = str_replace('✅ PASSED: ', '', $line);
        $htmlOutput .= '<div class="test-result passed">✅ ' . htmlspecialchars($testName) . '</div>';
        $testResults[] = ['name' => $testName, 'status' => 'passed'];
    } elseif (strpos($line, '❌ FAILED:') === 0) {
        // Formato clásico
        $testName = str_replace('❌ FAILED: ', '', $line);
        $htmlOutput .= '<div class="test-result failed">❌ ' . htmlspecialchars($testName) . '</div>';
        $testResults[] = ['name' => $testName, 'status' => 'failed'];
    } elseif (strpos($line, '[INFO]') !== false && strpos($line, 'Ejecutando') !== false) {
        // Formato del script seguro: [INFO] [X/Y] Test Name
        if (preg_match('/\[\d+\/\d+\]\s*(.+?)(?:\s*\([\d.]+\s*pts?\))?/', $line, $matches)) {
            $testName = $matches[1];
            $htmlOutput .= '<div style="padding: 5px; color: #666;">🔄 Ejecutando: ' . htmlspecialchars($testName) . '</div>';
        }
    } elseif (strpos($line, '🧪') !== false) {
        // Formato con emoji de test
        $testName = preg_replace('/^🧪[^:]*:\s*/', '', $line);
        $htmlOutput .= '<div style="padding: 5px; color: #666;">🔄 Ejecutando: ' . htmlspecialchars($testName) . '</div>';
    }
}

// Determinar color del score basado en porcentaje
$scoreClass = 'failed';
if ($percentage >= 90) $scoreClass = 'excellent';
elseif ($percentage >= 80) $scoreClass = 'good';
elseif ($percentage >= 70) $scoreClass = 'ok';
elseif ($percentage >= 60) $scoreClass = 'sufficient';

// Leer el reporte markdown si existe
$markdownReport = '';
$reportsPath = dirname(__DIR__) . '/reports/autograding-report.md';
if (file_exists($reportsPath)) {
    $markdownReport = file_get_contents($reportsPath);
}

?>
<style>
    .score-summary {
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
        text-align: center;
        font-size: 1.2em;
        font-weight: bold;
    }

    .excellent {
        background: #d4edda;
        color: #155724;
        border: 2px solid #28a745;
    }

    .good {
        background: #d1ecf1;
        color: #0c5460;
        border: 2px solid #17a2b8;
    }

    .ok {
        background: #fff3cd;
        color: #856404;
        border: 2px solid #ffc107;
    }

    .sufficient {
        background: #f8d7da;
        color: #721c24;
        border: 2px solid #fd7e14;
    }

    .failed {
        background: #f8d7da;
        color: #721c24;
        border: 2px solid #dc3545;
    }

    .test-result {
        padding: 10px;
        margin: 5px 0;
        border-radius: 4px;
        border-left: 4px solid #ccc;
    }

    .passed {
        background: #d4edda;
        color: #155724;
        border-left-color: #28a745;
    }

    .failed {
        background: #f8d7da;
        color: #721c24;
        border-left-color: #dc3545;
    }
</style>

<?php if ($score): ?>
    <div class="score-summary <?php echo $scoreClass; ?>">
        🎯 Puntuación Final: <?php echo $score; ?> puntos (<?php echo $percentage; ?>%)
        <br>
        <?php
        if ($percentage >= 90) echo "🎉 ¡EXCELENTE! - Trabajo excepcional";
        elseif ($percentage >= 80) echo "✅ ¡MUY BIEN! - Buen trabajo";
        elseif ($percentage >= 70) echo "👍 BIEN - Cumple con los requisitos";
        elseif ($percentage >= 60) echo "⚠️ SUFICIENTE - Necesita mejoras menores";
        else echo "❌ INSUFICIENTE - Requiere trabajo adicional";
        ?>
    </div>
<?php endif; ?>

<h3>Resultados Detallados:</h3>
<?php echo $htmlOutput; ?>

<?php if (!empty($htmlOutput)): ?>
    <h3>Resultados Detallados:</h3>
    <?php echo $htmlOutput; ?>
<?php else: ?>
    <div class="test-result failed">
        ⚠️ No se encontraron resultados de tests en el formato esperado.
    </div>
<?php endif; ?>

<!-- Información de debugging -->
<div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 4px; font-size: 0.9em; color: #666;">
    <strong>🔧 Información de Debug:</strong><br>
    • Script ejecutado: <code><?php echo htmlspecialchars($scriptName); ?></code><br>
    • Directorio: <code><?php echo htmlspecialchars(getcwd()); ?></code><br>
    • Tests detectados: <?php echo count($testResults); ?><br>
    • Salida total: <?php echo strlen($output); ?> caracteres<br>
    <?php if (empty($score)): ?>
        • ⚠️ No se detectó puntuación en la salida<br>
    <?php endif; ?>
</div>

<?php if (!empty($output)): ?>
    <h3>Log Completo de Ejecución:</h3>
    <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; max-height: 400px; overflow-y: auto;"><?php echo htmlspecialchars($output); ?></pre>
<?php endif; ?>

<?php if ($markdownReport): ?>
    <h3>Reporte Detallado:</h3>
    <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; max-height: 500px; overflow-y: auto;">
        <pre><?php echo htmlspecialchars($markdownReport); ?></pre>
    </div>
<?php endif; ?>

<div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 4px;">
    <strong>💡 Próximos pasos:</strong><br>
    <?php if ($percentage >= 60): ?>
        • ✅ ¡Felicitaciones! Has alcanzado el puntaje mínimo<br>
        • 📝 Revisa los tests que aún fallan para mejorar tu puntuación<br>
        • 🔄 Haz commit y push para que se ejecute el autograding oficial en GitHub
    <?php else: ?>
        • 📝 Revisa los tests que fallan y corrige tu código<br>
        • 🔧 Usa <code>composer style-fix</code> para corregir problemas de formato<br>
        • 🔍 Usa <code>composer analyze</code> para detectar problemas de código<br>
        • 🔄 Vuelve a ejecutar los tests hasta alcanzar el puntaje mínimo (15/25 puntos)
    <?php endif; ?>
</div>