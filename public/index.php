<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDH - Tecnodidácticahoy - Entorno de Laboratorio</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <a href="index.php" class="logo">🧪 TDH - Tecnodidácticahoy Lab</a>
            <ul class="nav-links">
                <li><a href="test-results.php">Tests</a></li>
                <li><a href="autograding-report.php">Autograding</a></li>
                <li><a href="phpinfo.php">PHP Info</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <section class="hero">
            <h1>🧪 TDH Testing Lab</h1>
            <p>Entorno profesional de desarrollo y testing automático para ejercicios PHP con evaluación continua.</p>
        </section>

        <div class="cards-grid">
            <div class="card">
                <div class="card-icon">📊</div>
                <h3>Estado del Sistema</h3>
                <div class="status-grid">
                    <div class="status success">
                        ✅ PHP <?php echo PHP_VERSION; ?>
                    </div>
                    <div class="status success">
                        ✅ Composer Activo
                    </div>
                    <div class="status success">
                        ✅ PHPUnit Configurado
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-icon">🚀</div>
                <h3>Acciones Rápidas</h3>
                <p>Herramientas esenciales para desarrollo y testing</p>
                <div class="btn-grid">
                    <a href="test-results.php" class="btn">📈 Ver Tests</a>
                    <a href="autograding-report.php" class="btn">📋 Autograding</a>
                </div>
            </div>

            <div class="card">
                <div class="card-icon">�</div>
                <h3>Ejercicios Disponibles</h3>
                <p>Ejercicios prácticos de PHP con tests automáticos</p>
                <div class="btn-grid">
                    <a href="ejercicio-4.php" class="btn">🧮 Ejercicio 4: Tabla de Multiplicar</a>
                </div>
            </div>

            <div class="card">
                <div class="card-icon">�📁</div>
                <h3>Estructura del Proyecto</h3>
                <p>Organización profesional para desarrollo PHP</p>
            </div>
        </div>

        <section class="code-section">
            <h2>📁 Estructura del Proyecto</h2>
            <pre>$PROJECT_ROOT/
├── exercises/          # Ejercicios para completar
├── solutions/          # Soluciones de referencia  
├── tests/             # Tests automáticos
├── public/            # Archivos web públicos
├── phpunit.xml        # Configuración de PHPUnit
└── composer.json      # Dependencias del proyecto</pre>
        </section>

        <section class="code-section">
            <h2>⚡ Comandos Útiles</h2>
            <pre># Ejecutar todos los tests
composer test

# Ejecutar tests con detalles
composer test-watch

# Análisis estático del código
composer analyze

# Verificar estilo de código PSR-12
composer style-check

# Corregir estilo automáticamente
composer style-fix

# Iniciar servidor de desarrollo
composer serve</pre>
        </section>

        <section class="code-section">
            <h2>🌐 Información de Desarrollo</h2>
            <div class="status-grid">
                <div class="status info">
                    🌐 Puerto 8000: Servidor PHP
                </div>
                <div class="status info">
                    🔄 Puerto 5500: Live Server
                </div>
                <div class="status info">
                    ⚡ Puerto 3000: Node.js (opcional)
                </div>
            </div>
        </section>
    </main>
</body>
</html>
