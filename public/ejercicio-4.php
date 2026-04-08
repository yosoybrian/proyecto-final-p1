<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 4: Tabla de Multiplicar</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .exercise-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .multiplication-table {
            background: #ffffff;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
        
        .table-line {
            font-family: 'Courier New', monospace;
            padding: 2px 0;
            color: #333;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #dc3545;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #28a745;
        }
        
        .input-group {
            margin: 15px 0;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .input-group input[type="number"] {
            width: 100px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="exercise-container">
        <h1>🧮 Ejercicio 4: Tabla de Multiplicar</h1>
        <p>Esta página demuestra la funcionalidad del ejercicio de tabla de multiplicar.</p>
        
        <form method="POST" action="">
            <div class="input-group">
                <label for="number">Número para la tabla de multiplicar (1-12):</label>
                <input type="number" id="number" name="number" min="1" max="12" value="<?= $_POST['number'] ?? 5 ?>" required>
                <button type="submit" class="btn">Generar Tabla</button>
            </div>
        </form>

        <hr>

        <?php
        require_once '../vendor/autoload.php';

        use Exercises\MultiplicationTableExample;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['number'])) {
            $number = intval($_POST['number']);
            
            if ($number >= 1 && $number <= 12) {
                try {
                    $table = new MultiplicationTableExample();
                    
                    echo "<div class='success'>";
                    echo "<h3>✅ Tabla del $number generada correctamente</h3>";
                    echo "</div>";
                    
                    // Mostrar tabla generada
                    echo "<div class='multiplication-table'>";
                    echo "<h4>Método generateTable($number):</h4>";
                    $tableArray = $table->generateTable($number);
                    echo "<pre>";
                    foreach ($tableArray as $line) {
                        echo "<div class='table-line'>$line</div>";
                    }
                    echo "</pre>";
                    echo "</div>";
                    
                    // Mostrar tabla formateada
                    echo "<div class='multiplication-table'>";
                    echo "<h4>Método displayTable():</h4>";
                    $formattedTable = $table->displayTable($tableArray);
                    echo "<pre>" . htmlspecialchars($formattedTable) . "</pre>";
                    echo "</div>";
                    
                    // Mostrar método combinado
                    echo "<div class='multiplication-table'>";
                    echo "<h4>Método createAndShowTable($number):</h4>";
                    $combinedResult = $table->createAndShowTable($number);
                    echo "<pre>" . htmlspecialchars($combinedResult) . "</pre>";
                    echo "</div>";
                    
                } catch (Exception $e) {
                    echo "<div class='error'>";
                    echo "<h3>❌ Error al generar la tabla</h3>";
                    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<div class='error'>";
                echo "<h3>❌ Número inválido</h3>";
                echo "<p>Por favor ingresa un número entre 1 y 12.</p>";
                echo "</div>";
            }
        } else {
            // Mostrar ejemplo por defecto
            try {
                $table = new MultiplicationTableExample();
                $defaultResult = $table->createAndShowTable(5);
                
                echo "<div class='multiplication-table'>";
                echo "<h3>Ejemplo: Tabla del 5</h3>";
                echo "<pre>" . htmlspecialchars($defaultResult) . "</pre>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='error'>";
                echo "<h3>❌ Error</h3>";
                echo "<p>La clase MultiplicationTable no está implementada aún.</p>";
                echo "<p>Completa la implementación en <code>exercises/MultiplicationTable.php</code></p>";
                echo "</div>";
            }
        }
        ?>

        <hr>

        <div class="multiplication-table">
            <h3>📋 Instrucciones del Ejercicio</h3>
            <ul>
                <li><strong>Archivo de trabajo:</strong> <code>exercises/MultiplicationTable.php</code></li>
                <li><strong>Archivo de tests:</strong> <code>tests/MultiplicationTableTest.php</code></li>
                <li><strong>Documentación:</strong> <code>tasks/ejercicio-4-tabla-multiplicar.md</code></li>
            </ul>
            
            <h4>Métodos a implementar:</h4>
            <ol>
                <li><code>generateTable($number)</code> - Genera array con operaciones</li>
                <li><code>displayTable($tableArray)</code> - Formatea array para mostrar</li>
                <li><code>createAndShowTable($number)</code> - Método combinado</li>
            </ol>
        </div>

        <div class="multiplication-table">
            <h3>🧪 Ejecutar Tests</h3>
            <p>Para probar tu implementación, ejecuta en la terminal:</p>
            <pre><code>composer test</code></pre>
            <p>O para ver detalles:</p>
            <pre><code>composer test-watch</code></pre>
        </div>
    </div>
</body>
</html>