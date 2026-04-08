<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Info - Configuración del Sistema</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <a href="index.php" class="logo">🧪 PHP Lab</a>
            <ul class="nav-links">
                <li><a href="test-results.php">Tests</a></li>
                <li><a href="autograding-report.php">Autograding</a></li>
                <li><a href="phpinfo.php">PHP Info</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <section class="hero">
            <h1>🔧 Información de PHP</h1>
            <p>Configuración completa del entorno PHP para desarrollo</p>
        </section>

        <div class="card">
            <div class="btn-group">
                <a href="index.php" class="btn">← Volver al Inicio</a>
                <a href="test-results.php" class="btn">📊 Ver Tests</a>
                <a href="autograding-report.php" class="btn">📋 Autograding</a>
            </div>
        </div>

        <div class="card">
            <h2>📋 Configuración del Sistema PHP</h2>
            <div style="background: white; border-radius: 12px; overflow: hidden;">
                <?php phpinfo(); ?>
            </div>
        </div>
    </main>
</body>
</html>
