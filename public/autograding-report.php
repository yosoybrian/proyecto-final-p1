<?php
// Ejecutar el script de autograding y mostrar resultados
$reportPath = '../reports/autograding-report.md';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>TDH - Tecnodidácticahoy - Reporte de Autograding</title>
    <link rel="stylesheet" href="assets/style.css">
    <script>
        function runAutograding() {
            window.location.hash = '#view-results';
            document.getElementById('results').innerHTML = '<div class="loading"><img src="images/loading.gif" alt="Loading" style="width: 80px; height: 80px;" /><p>🔄 Ejecutando autograding... esto puede tomar unos minutos.</p></div>';
            
            fetch('run-autograding.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('results').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('results').innerHTML = '<div class="test-result failed"><span>❌</span><div>Error al ejecutar autograding: ' + error + '</div></div>';
                });
        }
    </script>
</head>
<body>
    <img src="images/loading.gif" alt="Loading" style="display:none;" />
    <header class="header">
        <nav class="nav-container">
            <a href="index.php" class="logo">🧪 TDH Testing Lab</a>
            <ul class="nav-links">
                <li><a href="test-results.php">Tests</a></li>
                <li><a href="autograding-report.php">Autograding</a></li>
                <li><a href="phpinfo.php">PHP Info</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <section class="hero">
            <h1>📊 Reporte de Autograding</h1>
            <p>Sistema de evaluación automática para GitHub Classroom</p>
        </section>

        <div class="card">
            <div class="info-box">
                <strong>🎓 GitHub Classroom Autograding</strong><br>
                Este reporte simula la evaluación automática que se ejecuta en GitHub Classroom cuando haces commit de tu código.
            </div>
            
            <div class="btn-group">
                <button onclick="runAutograding()" class="btn success">🧪 Ejecutar Autograding</button>
                <a href="index.php" class="btn">← Volver al Inicio</a>
                <a href="test-results.php" class="btn">📈 Ver Tests Detallados</a>
            </div>
        </div>

        <div class="score-box">
            <h2>🎯 Sistema de Evaluación</h2>
            <?php
            // Calcular puntuación total dinámica desde autograding.json
            $autogradingFile = '../.github/classroom/autograding.json';
            $totalPoints = 0;
            $functionalPoints = 0;
            $qualityPoints = 0;
            
            if (file_exists($autogradingFile)) {
                $autogradingData = json_decode(file_get_contents($autogradingFile), true);
                
                if ($autogradingData && isset($autogradingData['tests'])) {
                    foreach ($autogradingData['tests'] as $test) {
                        $points = $test['points'] ?? 0;
                        $testName = $test['name'] ?? '';
                        $command = $test['run'] ?? '';
                        
                        $totalPoints += $points;
                        
                        // Categorizar puntos automáticamente
                        if (strpos($command, 'phpcs') !== false || strpos($command, 'phpstan') !== false || 
                            strpos($testName, 'PSR-12') !== false || strpos($testName, 'Estático') !== false ||
                            strpos($testName, 'Documentación') !== false) {
                            $qualityPoints += $points;
                        } else {
                            $functionalPoints += $points;
                        }
                    }
                }
            }
            
            // Fallback si no se puede leer el archivo
            if ($totalPoints == 0) {
                $totalPoints = 25;
                $functionalPoints = 15;
                $qualityPoints = 10;
            }
            ?>
            <p><strong>Puntuación Total Disponible:</strong> <?php echo number_format($totalPoints, 2); ?> puntos</p>
            <div class="score-grid">
                <div class="score-item">
                    <strong>⚡ Tests Funcionales</strong><br>
                    <?php echo number_format($functionalPoints, 2); ?> puntos
                </div>
                <div class="score-item">
                    <strong>✨ Calidad de Código</strong><br>
                    <?php echo number_format($qualityPoints, 2); ?> puntos
                </div>
            </div>
        </div>

        <div class="card">
            <h2>📋 Criterios de Evaluación</h2>
            <?php
            // Leer y procesar autograding.json dinámicamente
            $autogradingFile = '../.github/classroom/autograding.json';
            $totalPointsDisplay = 0;
            
            if (file_exists($autogradingFile)) {
                $autogradingData = json_decode(file_get_contents($autogradingFile), true);
                
                if ($autogradingData && isset($autogradingData['tests'])) {
                    foreach ($autogradingData['tests'] as $test) {
                        $testName = htmlspecialchars($test['name'] ?? 'Test sin nombre');
                        $points = number_format($test['points'] ?? 0, 2);
                        $totalPointsDisplay += $test['points'] ?? 0;
                        
                        // Generar descripción automática basada en el nombre y comando
                        $description = '';
                        $command = $test['run'] ?? '';
                        
                        if (strpos($testName, 'Suma') !== false) {
                            $description = 'Implementar método add() correctamente';
                        } elseif (strpos($testName, 'Resta') !== false) {
                            $description = 'Implementar método subtract() correctamente';
                        } elseif (strpos($testName, 'Multiplicación') !== false) {
                            $description = 'Implementar método multiply() correctamente';
                        } elseif (strpos($testName, 'División') !== false && strpos($testName, 'Cero') === false) {
                            $description = 'Implementar método divide() correctamente';
                        } elseif (strpos($testName, 'División por Cero') !== false) {
                            $description = 'Manejar excepciones apropiadamente';
                        } elseif (strpos($testName, 'Data Provider') !== false) {
                            $description = 'Funcionar con casos múltiples de datos';
                        } elseif (strpos($testName, 'PSR-12') !== false || strpos($command, 'phpcs') !== false) {
                            $description = 'Cumplir estándares de código PSR-12';
                        } elseif (strpos($testName, 'PHPStan') !== false || strpos($command, 'phpstan') !== false) {
                            $description = 'Código libre de errores detectables por análisis estático';
                        } elseif (strpos($testName, 'Final') !== false || strpos($testName, 'Todos') !== false) {
                            $description = 'Todos los tests pasan correctamente';
                        } elseif (strpos($testName, 'Documentación') !== false || strpos($command, 'README') !== false) {
                            $description = 'Documentación del proyecto completa';
                        } else {
                            $description = 'Test específico según configuración';
                        }
                        
                        echo '<div class="test-result passed">';
                        echo '<span>✅</span>';
                        echo '<div>';
                        echo '<strong>' . $testName . ' (' . $points . ' puntos)</strong><br>';
                        echo '<small>' . $description . '</small>';
                        echo '</div>';
                        echo '</div>' . "\n            ";
                    }
                    
                    // Mostrar total dinámico
                    echo '<div class="info-box" style="margin-top: 1.5rem;">';
                    echo '<strong>📊 Total: ' . number_format($totalPointsDisplay, 2) . ' puntos disponibles</strong>';
                    echo '</div>';
                } else {
                    echo '<div class="test-result failed">';
                    echo '<span>❌</span>';
                    echo '<strong>Error:</strong> No se pudieron cargar los tests desde autograding.json';
                    echo '</div>';
                }
            } else {
                // Fallback a contenido estático si no existe el archivo
                echo '<div class="test-result failed">';
                echo '<span>⚠️</span>';
                echo '<strong>Archivo autograding.json no encontrado</strong> - Usando configuración por defecto';
                echo '</div>';
                ?>
                <div class="test-result passed">
                    <span>✅</span>
                    <div>
                        <strong>Test Suma (2 puntos)</strong><br>
                        <small>Implementar método add() correctamente</small>
                    </div>
                </div>
                <div class="test-result passed">
                    <span>✅</span>
                    <div>
                        <strong>Test Resta (2 puntos)</strong><br>
                        <small>Implementar método subtract() correctamente</small>
                    </div>
                </div>
                <div class="test-result passed">
                    <span>✅</span>
                    <div>
                        <strong>Test Multiplicación (2 puntos)</strong><br>
                        <small>Implementar método multiply() correctamente</small>
                    </div>
                </div>
                <div class="test-result passed">
                    <span>✅</span>
                    <div>
                        <strong>Test División (2 puntos)</strong><br>
                        <small>Implementar método divide() correctamente</small>
                    </div>
                </div>
                <div class="test-result passed">
                    <span>✅</span>
                    <div>
                        <strong>Test División por Cero (2 puntos)</strong><br>
                        <small>Manejar excepciones apropiadamente</small>
                    </div>
                </div>
                <div class="test-result passed">
                    <span>✅</span>
                    <div>
                        <strong>Tests con Data Provider (5 puntos)</strong><br>
                        <small>Funcionar con casos múltiples</small>
                    </div>
                </div>
                <div class="test-result passed">
                    <span>✅</span>
                    <div>
                        <strong>Verificación PSR-12 (3 puntos)</strong><br>
                        <small>Cumplir estándares de código</small>
                    </div>
                </div>
                <div class="test-result passed">
                    <span>✅</span>
                    <div>
                        <strong>Análisis Estático (3 puntos)</strong><br>
                        <small>Código libre de errores detectables</small>
                    </div>
                </div>
                <div class="test-result passed">
                    <span>✅</span>
                    <div>
                        <strong>Verificación Final (4 puntos)</strong><br>
                        <small>Todos los tests pasan correctamente</small>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="card">
            <h2 id="view-results">📈 Resultados de Ejecución</h2>
            <div id="results">
                <div style="text-align: center; padding: 2rem; color: #6b7280;">
                    <p>Haz clic en "🧪 Ejecutar Autograding" para ver los resultados de evaluación en tiempo real.</p>
                </div>
            </div>
        </div>

        <div class="tips-box">
            <h2>💡 Tips para Estudiantes</h2>
            <ul>
                <li>📝 <strong>Lee los tests:</strong> Entiende qué se espera de cada método</li>
                <li>🔄 <strong>Ejecuta tests frecuentemente:</strong> Usa `composer test` para verificar progreso</li>
                <li>🎨 <strong>Cuida el estilo:</strong> Usa `composer style-fix` para corregir formato</li>
                <li>🔍 <strong>Analiza el código:</strong> Usa `composer analyze` para detectar problemas</li>
                <li>📋 <strong>Commit frecuentemente:</strong> El autograding se ejecuta en cada push</li>
            </ul>
        </div>

        <div class="resources-box">
            <h2>📚 Recursos de Ayuda</h2>
            <ul>
                <li><strong>📖 Documentación PHP:</strong> <a href="https://www.php.net/manual/" target="_blank">php.net/manual</a></li>
                <li><strong>📏 PSR-12 Standard:</strong> <a href="https://www.php-fig.org/psr/psr-12/" target="_blank">php-fig.org/psr/psr-12</a></li>
                <li><strong>🧪 PHPUnit Docs:</strong> <a href="https://phpunit.de/documentation.html" target="_blank">phpunit.de/documentation</a></li>
                <li><strong>🎓 GitHub Classroom:</strong> Ver resultados en la pestaña "Actions" de tu repositorio</li>
            </ul>
        </div>
    </main>
</body>
</html>
