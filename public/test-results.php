<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDH - Tecnodidácticahoy - Resultados de Tests</title>
    <link rel="stylesheet" href="assets/style.css">
    <script>
        function runTests() {
            document.getElementById('results').innerHTML = '<div class="loading">🔄 Ejecutando tests... Por favor espera.</div>';
            fetch('run-tests.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('results').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('results').innerHTML = '<div style="color: #dc2626;">❌ Error al ejecutar tests: ' + error + '</div>';
                });
        }
        
        // Auto-refresh cada 30 segundos
        setInterval(runTests, 30000);
    </script>
</head>
<body>
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
            <h1>📊 Resultados de Tests</h1>
            <p>Monitoreo en tiempo real de los tests automáticos de tu código</p>
        </section>

        <div class="card">
            <div class="refresh-info">
                <strong>🔄 Auto-refresh activo:</strong> Esta página se actualiza automáticamente cada 30 segundos para mostrarte los resultados más recientes.<br>
                También puedes hacer clic en "Ejecutar Tests" para una actualización manual inmediata.
            </div>
            
            <div class="btn-group">
                <button onclick="runTests()" class="btn success">🧪 Ejecutar Tests</button>
                <a href="autograding-report.php" class="btn">📋 Ver Autograding</a>
                <a href="index.php" class="btn">← Volver al Inicio</a>
            </div>
        </div>

        <div class="card">
            <h2>📈 Resultados de Ejecución</h2>
            <div id="results">
                <div class="loading">
                    <p>Haz clic en "🧪 Ejecutar Tests" para ver los resultados de tus tests en tiempo real.</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
