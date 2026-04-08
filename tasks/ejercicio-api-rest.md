# 🚀 API REST - Sistema de Gestión de Citas Médicas

## 👋 ¡Bienvenido!

Este ejercicio te guiará **paso a paso** para crear una API REST completa usando PHP, SQLite y Programación Orientada a Objetos.

### 🎯 ¿Qué Construirás?

Una API simple que permite:

-   ✅ Guardar pacientes en una base de datos
-   ✅ Ver la lista de pacientes
-   ✅ Agendar citas médicas
-   ✅ Ver las citas agendadas

**Tiempo estimado:** 3-4 horas

---

## 📚 Conceptos Básicos (¡Léelos Primero!)

### 🔹 ¿Qué es una API?

Una API es como un **mesero en un restaurante**:

-   **Tú** (cliente) pides comida
-   **El mesero** (API) lleva tu pedido a la cocina
-   **La cocina** (base de datos) prepara la comida
-   **El mesero** te trae tu comida

**En programación:**

-   Tú haces una petición: "Dame la lista de pacientes"
-   La API busca en la base de datos
-   La API te devuelve la información en formato JSON

### 🔹 ¿Qué es JSON?

Es un formato simple para enviar datos:

```json
{
    "nombre": "Juan",
    "edad": 25,
    "email": "juan@email.com"
}
```

### 🔹 ¿Qué son los Métodos HTTP?

Son "acciones" que puedes hacer:

| Método     | Acción     | Ejemplo                      |
| ---------- | ---------- | ---------------------------- |
| **GET**    | Ver/Leer   | Ver lista de pacientes       |
| **POST**   | Crear      | Crear un nuevo paciente      |
| **PUT**    | Actualizar | Cambiar datos de un paciente |
| **DELETE** | Eliminar   | Borrar un paciente           |

### 🔹 ¿Qué es una Base de Datos?

Es como un **Excel muy potente** donde guardamos información en tablas.

**Ejemplo de tabla `pacientes`:**

| id  | nombre | apellido | email           |
| --- | ------ | -------- | --------------- |
| 1   | Juan   | Pérez    | juan@email.com  |
| 2   | María  | García   | maria@email.com |

### 🔹 ¿Qué es PHP?

PHP es el lenguaje de programación que usaremos. Se ejecuta en el servidor (no en el navegador como JavaScript).

### 🔹 ¿Qué es una Clase en POO?

Una **clase** es como un molde para crear objetos:

```
Clase "Paciente" = Molde que define: nombre, apellido, email
Juan Pérez = Un paciente creado con ese molde
María García = Otro paciente creado con ese molde
```

---

## 🏗️ Parte 1: Preparar tu Espacio de Trabajo

### 📁 Paso 1.1: Crear carpetas

**¿Por qué?** Organizamos el código en carpetas, como organizas cuadernos por materia.

**Estructura final:**

```
exercises/api-ejercicio/          ← Carpeta principal de tu proyecto
├── src/                ← Todo tu código PHP aquí
│   ├── Config/         ← Configuración (base de datos)
│   ├── Repositorios/   ← Código para hablar con la BD
│   └── Controladores/  ← Código que recibe peticiones
├── public/             ← Archivos públicos (entrada de la API)
└── composer.json       ← Configuración del proyecto
```

## 📁 Estructura de Directorios

```
exercises/api-ejercicio/
├── src/
│   ├── Config/
│   │   └── Database.php
│   ├── Repositorios/
│   │   ├── PacienteRepository.php
│   │   └── CitaRepository.php
│   └── Controladores/
│       ├── PacienteController.php
│       └── CitaController.php
├── public/
│   ├── api.php
│   └── index.php
└── router.php
```

**Ejecuta estos comandos UNO POR UNO en la terminal:**

```bash
# Ir a la carpeta exercises
cd /workspaces/poo-api-citas-medica/exercises

# Crear todas las carpetas necesarias
mkdir -p api-ejercicio/src/Config
mkdir -p api-ejercicio/src/Repositorios
mkdir -p api-ejercicio/src/Controladores
mkdir -p api-ejercicio/public
```

💡 **¿Qué hace cada comando?**

-   `cd` = cambiar de directorio (como hacer doble clic en una carpeta)
-   `mkdir -p` = crear carpeta (y subcarpetas si es necesario)

---

### 📄 Paso 1.2: Crear composer.json

**¿Qué es Composer?** Es una herramienta que ayuda a organizar proyectos PHP.

**¿Qué es composer.json?** Es la "ficha de identidad" de tu proyecto.

**Crea el archivo:** `api-ejercicio/composer.json`

**Copia EXACTAMENTE este contenido:**

```json
{
    "name": "estudiante/mi-primera-api",
    "description": "Mi primera API para gestión de citas médicas",
    "type": "project",
    "require": {
        "php": ">=7.4"
    },
    "autoload": {
        "psr-4": {
            "ApiEjercicio\\": "src/"
        }
    }
}
```

**📖 Explicación línea por línea:**

| Línea              | ¿Qué hace?                             |
| ------------------ | -------------------------------------- |
| `"name"`           | Nombre de tu proyecto                  |
| `"description"`    | Breve descripción                      |
| `"require"`        | Lo que necesita tu proyecto (PHP 7.4+) |
| `"autoload"`       | Le dice a PHP dónde están tus archivos |
| `"ApiEjercicio\\"` | Namespace base (como el apellido)      |
| `"src/"`           | Carpeta donde está tu código           |

---

### ⚙️ Paso 1.3: Instalar dependencias

**Ejecuta este comando:**

```bash
cd api-ejercicio
composer install
```

**¿Qué pasó?**

-   ✅ Se creó una carpeta `vendor/` (archivos auxiliares, NO los toques)
-   ✅ Se creó `vendor/autoload.php` (carga tus clases automáticamente)

Si ves este mensaje, ¡perfecto!:

```
Generating autoload files
```

---

## 🗄️ Parte 2: Crear la Base de Datos

### 🎯 Objetivo

Crear una clase `Database` que:

1. Se conecte a SQLite (base de datos simple)
2. Cree las tablas automáticamente
3. Nos dé acceso a la conexión

---

### 📝 Paso 2.1: Entender qué haremos

**Vamos a crear 2 tablas:**

**Tabla `pacientes`:**

-   `id` - Número único (1, 2, 3...)
-   `nombre` - Texto
-   `apellido` - Texto
-   `email` - Texto único
-   `telefono` - Texto (opcional)
-   `fecha_nacimiento` - Fecha (opcional)

**Tabla `citas`:**

-   `id` - Número único
-   `paciente_id` - ¿Qué paciente? (referencia a tabla pacientes)
-   `fecha_hora` - ¿Cuándo?
-   `motivo` - ¿Por qué?
-   `estado` - pendiente/confirmada/cancelada

---

### 📝 Paso 2.2: Crear Database.php

**Crea el archivo:** `src/Config/Database.php`

**Analiza y realiza todo este codigo .:**

```php
<?php

// Declarar en qué "carpeta virtual" está esta clase
namespace ApiEjercicio\Config;

/**
 * Clase Database
 * Maneja la conexión a la base de datos SQLite
 */
class Database
{
    // Variable compartida por toda la clase (solo una instancia)
    private static $instance = null;

    // Variable para guardar la conexión
    private $conexion;

    /**
     * Constructor PRIVADO
     * ¿Por qué privado? Para que solo haya UNA conexión
     */
    private function __construct()
    {
        // __DIR__ = Esta carpeta (Config)
        // '/../../' = Subir 2 niveles
        $dbPath = __DIR__ . '/../../database.sqlite';

        // Crear conexión PDO a SQLite
        // PDO = PHP Data Objects (forma estándar de conectar a BD)
        $this->conexion = new \PDO('sqlite:' . $dbPath);

        // Configurar para mostrar errores
        $this->conexion->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Retornar arrays asociativos
        $this->conexion->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        // Crear las tablas
        $this->crearTablas();
    }

    /**
     * Método para obtener LA ÚNICA instancia
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener la conexión
     */
    public function getConexion()
    {
        return $this->conexion;
    }

    /**
     * Crear tablas si no existen
     */
    private function crearTablas()
    {
        // Tabla de pacientes
        $this->conexion->exec("
            CREATE TABLE IF NOT EXISTS pacientes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                apellido TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                telefono TEXT,
                fecha_nacimiento DATE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Tabla de citas
        $this->conexion->exec("
            CREATE TABLE IF NOT EXISTS citas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                paciente_id INTEGER NOT NULL,
                fecha_hora DATETIME NOT NULL,
                motivo TEXT NOT NULL,
                estado TEXT DEFAULT 'pendiente',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
            )
        ");
    }
}
```

---

### 📖 Explicación del Código

#### 🔹 ¿Qué es `namespace`?

```php
namespace ApiEjercicio\Config;
```

Es como tu apellido. Si hay dos "Juan", usamos "Juan García" y "Juan López". Aquí usamos `ApiEjercicio\Config\Database`.

#### 🔹 ¿Qué es `private static $instance`?

-   `private` = solo esta clase puede verlo
-   `static` = compartido por todos (no individual)
-   `$instance` = guarda la única conexión

#### 🔹 ¿Por qué el constructor es `private`?

Para que nadie haga:

```php
new Database();  // ❌ Esto no funcionará
```

Solo se puede usar:

```php
Database::getInstance();  // ✅ Correcto
```

Esto garantiza **una sola conexión** (patrón Singleton).

#### 🔹 ¿Qué significa cada tipo de campo SQL?

| Tipo            | Significado                        | Ejemplo             |
| --------------- | ---------------------------------- | ------------------- |
| `INTEGER`       | Número entero                      | 1, 2, 3             |
| `TEXT`          | Texto                              | "Juan Pérez"        |
| `DATE`          | Fecha                              | 2024-12-01          |
| `DATETIME`      | Fecha y hora                       | 2024-12-01 10:30:00 |
| `PRIMARY KEY`   | Identificador único                | id                  |
| `AUTOINCREMENT` | Se incrementa automáticamente      | 1, 2, 3...          |
| `NOT NULL`      | Obligatorio (no puede estar vacío) | -                   |
| `UNIQUE`        | No se puede repetir                | email               |
| `DEFAULT`       | Valor por defecto                  | 'pendiente'         |
| `FOREIGN KEY`   | Referencia a otra tabla            | paciente_id         |

#### 🔹 ¿Qué es `ON DELETE CASCADE`?

Si eliminas un paciente, **automáticamente** se eliminan sus citas.

---

### ✅ Verifica tu Trabajo

**Checklist:**

-   [ ] Creaste la carpeta `src/Config/`
-   [ ] Creaste el archivo `Database.php` en esa carpeta
-   [ ] Copiaste TODO el código (incluyendo las llaves `}` finales)
-   [ ] No hay errores de sintaxis (no olvidaste `;` o `}`)

---

## 📦 Parte 3: Crear el Repository de Pacientes

### 🤔 ¿Qué es un Repository?

Un **Repository** es una clase que "habla" con la base de datos. Es como un **bibliotecario**:

-   Tú: "Quiero el libro 'Don Quijote'"
-   Bibliotecario: Va, lo busca, te lo trae
-   Tú no necesitas saber dónde está exactamente

**El Repository hace operaciones CRUD:**

| Operación  | Significado | Método                             |
| ---------- | ----------- | ---------------------------------- |
| **C**reate | Crear       | `crear()`                          |
| **R**ead   | Leer        | `obtenerTodos()`, `obtenerPorId()` |
| **U**pdate | Actualizar  | `actualizar()`                     |
| **D**elete | Eliminar    | `eliminar()`                       |

---

### 📝 Paso 3.1: Crear PacienteRepository

**Crea el archivo:** `src/Repositorios/PacienteRepository.php`

**Analiza y realiza todo este codigo . (con MUCHOS comentarios explicativos):**

```php
<?php

namespace ApiEjercicio\Repositorios;

use ApiEjercicio\Config\Database;

/**
 * PacienteRepository
 * Maneja TODAS las operaciones con la tabla 'pacientes'
 */
class PacienteRepository
{
    private $db;  // Variable para guardar la conexión

    /**
     * Constructor
     * Se ejecuta automáticamente al hacer: new PacienteRepository()
     */
    public function __construct()
    {
        // Obtener la conexión a la base de datos
        $this->db = Database::getInstance()->getConexion();
    }

    /**
     * 📖 OBTENER TODOS - Lista completa de pacientes
     *
     * SQL: SELECT * FROM pacientes ORDER BY id DESC
     * Retorna: Array de arrays
     */
    public function obtenerTodos()
    {
        // query() = ejecutar consulta simple
        $stmt = $this->db->query("SELECT * FROM pacientes ORDER BY id DESC");

        // fetchAll() = traer TODOS los resultados
        return $stmt->fetchAll();
    }

    /**
     * 🔍 OBTENER UNO - Buscar un paciente por ID
     *
     * @param int $id - El ID del paciente
     * @return array|false - Datos del paciente o false si no existe
     */
    public function obtenerPorId($id)
    {
        // prepare() = preparar consulta (más seguro)
        // ? = placeholder que será reemplazado
        $stmt = $this->db->prepare("SELECT * FROM pacientes WHERE id = ?");

        // execute() = ejecutar, reemplazando ? por $id
        $stmt->execute([$id]);

        // fetch() = traer UN SOLO resultado
        return $stmt->fetch();
    }

    /**
     * ✍️ CREAR - Insertar un nuevo paciente
     *
     * @param array $datos - Información del paciente
     * @return int - El ID del paciente creado
     */
    public function crear($datos)
    {
        // Preparar consulta INSERT
        $stmt = $this->db->prepare("
            INSERT INTO pacientes (nombre, apellido, email, telefono, fecha_nacimiento)
            VALUES (?, ?, ?, ?, ?)
        ");

        // Ejecutar con los valores
        // ?? null = "si no existe, usa null"
        $stmt->execute([
            $datos['nombre'],                    // 1er ?
            $datos['apellido'],                  // 2do ?
            $datos['email'],                     // 3er ?
            $datos['telefono'] ?? null,          // 4to ? (opcional)
            $datos['fecha_nacimiento'] ?? null   // 5to ? (opcional)
        ]);

        // Retornar el ID del registro insertado
        return $this->db->lastInsertId();
    }

    /**
     * 🔄 ACTUALIZAR - Modificar un paciente existente
     *
     * @param int $id - ID del paciente a actualizar
     * @param array $datos - Nuevos datos
     * @return bool - true si se actualizó
     */
    public function actualizar($id, $datos)
    {
        $stmt = $this->db->prepare("
            UPDATE pacientes
            SET nombre = ?, apellido = ?, email = ?, telefono = ?, fecha_nacimiento = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['telefono'] ?? null,
            $datos['fecha_nacimiento'] ?? null,
            $id  // Último ? (para WHERE)
        ]);
    }

    /**
     * 🗑️ ELIMINAR - Borrar un paciente
     *
     * @param int $id - ID del paciente a eliminar
     * @return bool - true si se eliminó
     */
    public function eliminar($id)
    {
        $stmt = $this->db->prepare("DELETE FROM pacientes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
```

---

### 📖 Conceptos Importantes

#### 🔹 ¿Qué es un Prepared Statement?

**❌ FORMA PELIGROSA:**

```php
$sql = "SELECT * FROM pacientes WHERE id = $id";
```

**✅ FORMA SEGURA:**

```php
$stmt = $this->db->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$id]);
```

Esto previene **SQL Injection** (ataque de hackers).

#### 🔹 Diferencia entre `fetch()` y `fetchAll()`

```php
// fetch() retorna UN resultado:
['id' => 1, 'nombre' => 'Juan', 'apellido' => 'Pérez']

// fetchAll() retorna VARIOS resultados:
[
    ['id' => 1, 'nombre' => 'Juan', 'apellido' => 'Pérez'],
    ['id' => 2, 'nombre' => 'María', 'apellido' => 'García']
]
```

#### 🔹 ¿Qué es el operador `??`?

```php
$datos['telefono'] ?? null
```

Significa: "Si `$datos['telefono']` existe, úsalo. Si no, usa `null`"

Es igual a:

```php
isset($datos['telefono']) ? $datos['telefono'] : null
```

Pero más corto.

---

### ✅ Verifica tu Trabajo

-   [ ] Creaste `src/Repositorios/PacienteRepository.php`
-   [ ] Copiaste TODO el código
-   [ ] Entiendes qué hace cada método (relee si es necesario)

---

## 📦 Parte 4: EJERCICIO - Crear CitaRepository

**🎯 ¡Ahora te toca a ti!** Crearás el Repository para Citas siguiendo el patrón de Pacientes.

### 📝 Paso 4.1: Crear el archivo

**Crea:** `src/Repositorios/CitaRepository.php`

**Copia este código y COMPLETA los TODOs:**

```php
<?php

namespace ApiEjercicio\Repositorios;

use ApiEjercicio\Config\Database;

class CitaRepository
{
    private $db;

    // ========================================
    // TODO 1: Constructor
    // INSTRUCCIÓN: Copia el constructor de PacienteRepository
    // ========================================
    public function __construct()
    {
        // ESCRIBE AQUÍ (1 línea)

    }

    // ========================================
    // TODO 2: Obtener todas las citas
    // INSTRUCCIÓN: Queremos combinar datos de citas Y pacientes
    // Usa LEFT JOIN para unir las tablas
    // ========================================
    public function obtenerTodos()
    {
        // COPIA Y COMPLETA:
        $stmt = $this->db->query("
            SELECT c.*,
                   p.nombre as paciente_nombre,
                   p.apellido as paciente_apellido,
                   p.email as paciente_email
            FROM citas c
            LEFT JOIN pacientes p ON c.paciente_id = p.id
            ORDER BY c.fecha_hora DESC
        ");

        return $stmt->fetchAll();
    }

    // ========================================
    // TODO 3: Obtener una cita por ID
    // INSTRUCCIÓN: Similar a TODO 2 pero con WHERE c.id = ?
    // Usa prepare(), execute() y fetch()
    // ========================================
    public function obtenerPorId($id)
    {
        // ESCRIBE AQUÍ (4-5 líneas)

    }

    // ========================================
    // TODO 4: Crear una nueva cita
    // INSTRUCCIÓN: INSERT con 4 campos
    // Campos: paciente_id, fecha_hora, motivo, estado
    // ========================================
    public function crear($datos)
    {
        // ESCRIBE AQUÍ (6-8 líneas)
        // Pista: $datos['estado'] ?? 'pendiente'

    }

    // ========================================
    // TODO 5: Actualizar una cita
    // INSTRUCCIÓN: UPDATE de los 4 campos
    // ========================================
    public function actualizar($id, $datos)
    {
        // ESCRIBE AQUÍ (5-7 líneas)

    }

    // ========================================
    // TODO 6: Eliminar una cita
    // INSTRUCCIÓN: DELETE simple
    // ========================================
    public function eliminar($id)
    {
        // ESCRIBE AQUÍ (2 líneas)

    }
}
```

---

### 💡 AYUDA: Soluciones para los TODOs

<details>
<summary>👉 Click aquí para ver las soluciones (intenta primero por tu cuenta)</summary>

#### TODO 1: Constructor

```php
public function __construct()
{
    $this->db = Database::getInstance()->getConexion();
}
```

#### TODO 2: Ya está completo (para que veas el ejemplo)

#### TODO 3: obtenerPorId()

```php
public function obtenerPorId($id)
{
    $stmt = $this->db->prepare("
        SELECT c.*,
               p.nombre as paciente_nombre,
               p.apellido as paciente_apellido,
               p.email as paciente_email
        FROM citas c
        LEFT JOIN pacientes p ON c.paciente_id = p.id
        WHERE c.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
```

#### TODO 4: crear()

```php
public function crear($datos)
{
    $stmt = $this->db->prepare("
        INSERT INTO citas (paciente_id, fecha_hora, motivo, estado)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $datos['paciente_id'],
        $datos['fecha_hora'],
        $datos['motivo'],
        $datos['estado'] ?? 'pendiente'
    ]);

    return $this->db->lastInsertId();
}
```

#### TODO 5: actualizar()

```php
public function actualizar($id, $datos)
{
    $stmt = $this->db->prepare("
        UPDATE citas
        SET paciente_id = ?, fecha_hora = ?, motivo = ?, estado = ?
        WHERE id = ?
    ");

    return $stmt->execute([
        $datos['paciente_id'],
        $datos['fecha_hora'],
        $datos['motivo'],
        $datos['estado'] ?? 'pendiente',
        $id
    ]);
}
```

#### TODO 6: eliminar()

```php
public function eliminar($id)
{
    $stmt = $this->db->prepare("DELETE FROM citas WHERE id = ?");
    return $stmt->execute([$id]);
}
```

</details>

---

### 📖 Concepto Nuevo: LEFT JOIN

**¿Qué es un JOIN?**

Combina información de 2 tablas relacionadas.

**Ejemplo:**

```
Tabla CITAS:              Tabla PACIENTES:
id | paciente_id | motivo | ...    id | nombre | ...
1  | 5           | Gripe  | ...    5  | Juan   | ...
2  | 5           | Checkup| ...    6  | María  | ...
```

**Con LEFT JOIN obtenemos:**

```
id | paciente_id | motivo  | paciente_nombre
1  | 5           | Gripe   | Juan
2  | 5           | Checkup | Juan
```

**Sintaxis:**

```sql
SELECT c.*, p.nombre as paciente_nombre
FROM citas c
LEFT JOIN pacientes p ON c.paciente_id = p.id
```

-   `c` = alias (apodo) para tabla citas
-   `p` = alias para tabla pacientes
-   `ON c.paciente_id = p.id` = condición de unión

---

### ✅ Lista de Verificación

-   [ ] Completaste TODO 1 (constructor)
-   [ ] Completaste TODO 3 (obtenerPorId con JOIN)
-   [ ] Completaste TODO 4 (crear con INSERT)
-   [ ] Completaste TODO 5 (actualizar con UPDATE)
-   [ ] Completaste TODO 6 (eliminar con DELETE)
-   [ ] Probaste comparar tu código con las soluciones
-   [ ] Entiendes qué es un LEFT JOIN

---

## 🎮 Parte 5: Crear los Controladores

### 🤔 ¿Qué es un Controller?

Un **Controlador** es como el "gerente" de tu API. Decide qué hacer cuando llega una petición.

**Flujo:**

1. Llega petición: `GET /api/pacientes`
2. El Controlador dice: "Ok, necesito la lista de pacientes"
3. Llama al Repository: `$this->repository->obtenerTodos()`
4. Recibe los datos
5. Los convierte a JSON y responde

---

### 📝 Paso 5.1: Crear PacienteController

**Crea:** `src/Controladores/PacienteController.php`

**Analiza y realiza todo este codigo .:**

```php
<?php

namespace ApiEjercicio\Controladores;

use ApiEjercicio\Repositorios\PacienteRepository;

class PacienteController
{
    private $repository;

    public function __construct()
    {
        $this->repository = new PacienteRepository();
    }

    /**
     * GET /api/pacientes
     * Listar todos los pacientes
     */
    public function index()
    {
        try {
            $pacientes = $this->repository->obtenerTodos();

            $this->responderJSON([
                'success' => true,
                'data' => $pacientes,
                'total' => count($pacientes)
            ]);
        } catch (\Exception $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/pacientes/{id}
     * Obtener un paciente específico
     */
    public function show($id)
    {
        try {
            $paciente = $this->repository->obtenerPorId($id);

            if ($paciente) {
                $this->responderJSON([
                    'success' => true,
                    'data' => $paciente
                ]);
            } else {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
            }
        } catch (\Exception $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/pacientes
     * Crear un nuevo paciente
     */
    public function store()
    {
        try {
            // Leer datos JSON del cuerpo de la petición
            $datos = json_decode(file_get_contents('php://input'), true);

            // VALIDAR campos obligatorios
            if (!isset($datos['nombre']) || !isset($datos['apellido']) || !isset($datos['email'])) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Faltan campos obligatorios: nombre, apellido, email'
                ], 400);
                return;
            }

            // VALIDAR formato de email
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Email inválido'
                ], 400);
                return;
            }

            // Crear paciente
            $id = $this->repository->crear($datos);
            $paciente = $this->repository->obtenerPorId($id);

            $this->responderJSON([
                'success' => true,
                'message' => 'Paciente creado exitosamente',
                'data' => $paciente
            ], 201);

        } catch (\Exception $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/pacientes/{id}
     * Actualizar un paciente
     */
    public function update($id)
    {
        try {
            $datos = json_decode(file_get_contents('php://input'), true);

            // Verificar que existe
            if (!$this->repository->obtenerPorId($id)) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
                return;
            }

            $this->repository->actualizar($id, $datos);
            $paciente = $this->repository->obtenerPorId($id);

            $this->responderJSON([
                'success' => true,
                'message' => 'Paciente actualizado',
                'data' => $paciente
            ]);

        } catch (\Exception $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/pacientes/{id}
     * Eliminar un paciente
     */
    public function destroy($id)
    {
        try {
            if (!$this->repository->obtenerPorId($id)) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
                return;
            }

            $this->repository->eliminar($id);

            $this->responderJSON([
                'success' => true,
                'message' => 'Paciente eliminado'
            ]);

        } catch (\Exception $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método auxiliar para responder en JSON
     */
    private function responderJSON($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
```

---

### 📖 Conceptos del Controller

#### 🔹 ¿Qué es `try-catch`?

```php
try {
    // Código que puede fallar
} catch (\Exception $e) {
    // Si falla, capturar el error
}
```

Es como un **paracaídas de seguridad**. Si algo falla, no se rompe todo.

#### 🔹 Códigos HTTP

| Código | Significado                                |
| ------ | ------------------------------------------ |
| 200    | OK - Todo bien                             |
| 201    | Created - Se creó algo nuevo               |
| 400    | Bad Request - Datos inválidos              |
| 404    | Not Found - No encontrado                  |
| 500    | Internal Server Error - Error del servidor |

#### 🔹 ¿Qué es `file_get_contents('php://input')`?

Lee el cuerpo de la petición HTTP (donde vienen los datos JSON).

#### 🔹 ¿Qué es `json_decode()`?

Convierte JSON a array PHP:

```php
// JSON:
'{"nombre": "Juan", "edad": 25}'

// Después de json_decode():
['nombre' => 'Juan', 'edad' => 25]
```

---

### ✅ Verifica tu Trabajo

-   [ ] Creaste `src/Controladores/PacienteController.php`
-   [ ] Copiaste TODO el código
-   [ ] Entiendes el flujo: petición → validar → repository → responder

---

## 🎮 Parte 6: EJERCICIO - Crear CitaController

**🎯 ¡Tu turno otra vez!** Crea el controlador para Citas.

### 📝 Paso 6.1: Crear el archivo

**Crea:** `src/Controladores/CitaController.php`

```php
<?php

namespace ApiEjercicio\Controladores;

use ApiEjercicio\Repositorios\CitaRepository;
use ApiEjercicio\Repositorios\PacienteRepository;

class CitaController
{
    private $repository;
    private $pacienteRepository;

    // ========================================
    // TODO 1: Constructor
    // INSTRUCCIÓN: Inicializa ambos repositorios
    // ========================================
    public function __construct()
    {
        // ESCRIBE AQUÍ (2 líneas)

    }

    // ========================================
    // TODO 2: Método index()
    // INSTRUCCIÓN: Copia de PacienteController pero cambia "pacientes" por "citas"
    // ========================================
    public function index()
    {
        // ESCRIBE AQUÍ

    }

    // ========================================
    // TODO 3: Método show()
    // INSTRUCCIÓN: Copia de PacienteController cambiando los mensajes
    // ========================================
    public function show($id)
    {
        // ESCRIBE AQUÍ

    }

    // ========================================
    // TODO 4: Método store() - EL MÁS IMPORTANTE
    // INSTRUCCIÓN: Además de validar campos, debes:
    // 1. Validar que el paciente existe
    // 2. Validar el formato de fecha (Y-m-d H:i:s)
    // ========================================
    public function store()
    {
        try {
            $datos = json_decode(file_get_contents('php://input'), true);

            // VALIDACIÓN 1: Campos obligatorios
            if (!isset($datos['paciente_id']) || !isset($datos['fecha_hora']) || !isset($datos['motivo'])) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Faltan campos: paciente_id, fecha_hora, motivo'
                ], 400);
                return;
            }

            // VALIDACIÓN 2: El paciente debe existir
            // COMPLETA AQUÍ (3-4 líneas)


            // VALIDACIÓN 3: Formato de fecha
            $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $datos['fecha_hora']);
            if (!$fecha) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Formato de fecha inválido. Use: YYYY-MM-DD HH:MM:SS'
                ], 400);
                return;
            }

            // Crear cita
            // COMPLETA AQUÍ (3-4 líneas)


        } catch (\Exception $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // TODO 5: Método update()
    // INSTRUCCIÓN: Copia de PacienteController con validación de paciente
    // ========================================
    public function update($id)
    {
        // ESCRIBE AQUÍ

    }

    // ========================================
    // TODO 6: Método destroy()
    // INSTRUCCIÓN: Copia de PacienteController cambiando mensajes
    // ========================================
    public function destroy($id)
    {
        // ESCRIBE AQUÍ

    }

    // ========================================
    // TODO 7: Método responderJSON()
    // INSTRUCCIÓN: Copia EXACTAMENTE de PacienteController
    // ========================================
    private function responderJSON($data, $statusCode = 200)
    {
        // ESCRIBE AQUÍ

    }
}
```

---

### 💡 AYUDA: Soluciones

<details>
<summary>👉 Click para ver las soluciones</summary>

#### TODO 1: Constructor

```php
public function __construct()
{
    $this->repository = new CitaRepository();
    $this->pacienteRepository = new PacienteRepository();
}
```

#### TODO 2: index() - Completar

```php
public function index()
{
    try {
        $citas = $this->repository->obtenerTodos();

        $this->responderJSON([
            'success' => true,
            'data' => $citas,
            'total' => count($citas)
        ]);
    } catch (\Exception $e) {
        $this->responderJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
```

#### TODO 4: store() - Completar validación de paciente

```php
// VALIDACIÓN 2: El paciente debe existir
if (!$this->pacienteRepository->obtenerPorId($datos['paciente_id'])) {
    $this->responderJSON([
        'success' => false,
        'message' => 'El paciente no existe'
    ], 404);
    return;
}

// Crear cita
$id = $this->repository->crear($datos);
$cita = $this->repository->obtenerPorId($id);

$this->responderJSON([
    'success' => true,
    'message' => 'Cita creada exitosamente',
    'data' => $cita
], 201);
```

#### TODO 7: responderJSON()

```php
private function responderJSON($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
```

</details>

---

## 🌐 Parte 7: Crear el Router (API Principal)

### 🤔 ¿Qué es un Router?

El **Router** es como el **director de tráfico**. Decide a dónde va cada petición.

**Ejemplo:**

-   Petición: `GET /api/pacientes` → PacienteController::index()
-   Petición: `POST /api/citas` → CitaController::store()

---

### 📝 Paso 7.1: Crear api.php

**Crea:** `public/api.php`

**Analiza y realiza todo este codigo .:**

```php
<?php

// Cargar autoload de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Importar controladores
use ApiEjercicio\Controladores\PacienteController;
use ApiEjercicio\Controladores\CitaController;

// Configurar CORS (permitir peticiones desde cualquier origen)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Si es petición OPTIONS, responder y salir
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Obtener método HTTP (GET, POST, PUT, DELETE)
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Obtener URI (ruta) y limpiarla
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim($uri, '/'));

try {
    // ========================================
    // ROUTER PARA PACIENTES
    // ========================================
    if (isset($uri[0]) && $uri[0] === 'api' && isset($uri[1]) && $uri[1] === 'pacientes') {

        $controller = new PacienteController();

        // GET /api/pacientes - Listar todos
        if ($requestMethod === 'GET' && !isset($uri[2])) {
            $controller->index();
        }
        // GET /api/pacientes/{id} - Ver uno
        elseif ($requestMethod === 'GET' && isset($uri[2])) {
            $controller->show($uri[2]);
        }
        // POST /api/pacientes - Crear
        elseif ($requestMethod === 'POST') {
            $controller->store();
        }
        // PUT /api/pacientes/{id} - Actualizar
        elseif ($requestMethod === 'PUT' && isset($uri[2])) {
            $controller->update($uri[2]);
        }
        // DELETE /api/pacientes/{id} - Eliminar
        elseif ($requestMethod === 'DELETE' && isset($uri[2])) {
            $controller->destroy($uri[2]);
        }

        exit;
    }

    // ========================================
    // TODO: ROUTER PARA CITAS
    // INSTRUCCIÓN: Copia el router de pacientes y cambia:
    // - 'pacientes' por 'citas'
    // - PacienteController por CitaController
    // ========================================

    // ESCRIBE AQUÍ TU CÓDIGO


    // Si llegamos aquí, la ruta no existe
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Ruta no encontrada'
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
```

---

### 💡 AYUDA: Router para Citas

<details>
<summary>👉 Click para ver la solución</summary>

```php
// ROUTER PARA CITAS
if (isset($uri[0]) && $uri[0] === 'api' && isset($uri[1]) && $uri[1] === 'citas') {

    $controller = new CitaController();

    // GET /api/citas - Listar todos
    if ($requestMethod === 'GET' && !isset($uri[2])) {
        $controller->index();
    }
    // GET /api/citas/{id} - Ver uno
    elseif ($requestMethod === 'GET' && isset($uri[2])) {
        $controller->show($uri[2]);
    }
    // POST /api/citas - Crear
    elseif ($requestMethod === 'POST') {
        $controller->store();
    }
    // PUT /api/citas/{id} - Actualizar
    elseif ($requestMethod === 'PUT' && isset($uri[2])) {
        $controller->update($uri[2]);
    }
    // DELETE /api/citas/{id} - Eliminar
    elseif ($requestMethod === 'DELETE' && isset($uri[2])) {
        $controller->destroy($uri[2]);
    }

    exit;
}
```

</details>

---

### 📝 Paso 7.2: Crear router.php (servidor de desarrollo)

**Crea:** `router.php` (en la raíz de `api-ejercicio/`)

```php
<?php

// Router para el servidor de desarrollo de PHP

// Si el archivo solicitado existe y es un archivo, servirlo
if (php_sapi_name() === 'cli-server' && is_file($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'])) {
    return false;
}

// Si la ruta comienza con /api, redirigir a api.php
if (strpos($_SERVER['REQUEST_URI'], '/api') === 0) {
    require_once __DIR__ . '/public/api.php';
} else {
    // Servir index.php para otras rutas
    require_once __DIR__ . '/public/index.php';
}
```

---

### 📝 Paso 7.3: Crear index.php (página de bienvenida)

**Crea:** `public/index.php`

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Primera API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #2c3e50; }
        .endpoint {
            background: #ecf0f1;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: monospace;
        }
        .method {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            margin-right: 10px;
        }
        .get { background: #3498db; color: white; }
        .post { background: #2ecc71; color: white; }
        .put { background: #f39c12; color: white; }
        .delete { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 ¡Tu API está funcionando!</h1>
        <p>Endpoints disponibles:</p>

        <h2>👤 Pacientes</h2>
        <div class="endpoint">
            <span class="method get">GET</span>
            /api/pacientes - Listar todos
        </div>
        <div class="endpoint">
            <span class="method get">GET</span>
            /api/pacientes/{id} - Ver uno
        </div>
        <div class="endpoint">
            <span class="method post">POST</span>
            /api/pacientes - Crear
        </div>
        <div class="endpoint">
            <span class="method put">PUT</span>
            /api/pacientes/{id} - Actualizar
        </div>
        <div class="endpoint">
            <span class="method delete">DELETE</span>
            /api/pacientes/{id} - Eliminar
        </div>

        <h2>📅 Citas</h2>
        <div class="endpoint">
            <span class="method get">GET</span>
            /api/citas - Listar todas
        </div>
        <div class="endpoint">
            <span class="method get">GET</span>
            /api/citas/{id} - Ver una
        </div>
        <div class="endpoint">
            <span class="method post">POST</span>
            /api/citas - Crear
        </div>
        <div class="endpoint">
            <span class="method put">PUT</span>
            /api/citas/{id} - Actualizar
        </div>
        <div class="endpoint">
            <span class="method delete">DELETE</span>
            /api/citas/{id} - Eliminar
        </div>
    </div>
</body>
</html>
```

---

## 🧪 Parte 8: Probar tu API

### 📝 Paso 8.1: Ejecutar Tests Automáticos

**Antes de iniciar el servidor, verifica que tu código funciona correctamente:**

```bash
cd /workspaces/poo-api-citas-medica
composer test:api
```

**Los tests verifican:**
- ✅ Que la clase Database se conecta correctamente
- ✅ Que las tablas se crean automáticamente
- ✅ Que los Repositories funcionan (CRUD completo)
- ✅ Que los Controladores responden correctamente
- ✅ Que la API responde a peticiones HTTP

**Si todos los tests pasan, ¡tu código está correcto!** 🎉

---

### 📝 Paso 8.2: Iniciar el servidor

**Ejecuta en la terminal:**

```bash
cd /workspaces/poo-api-citas-medica/exercises/api-ejercicio
php -S localhost:8000 router.php
```

Deberías ver:

```
[Wed Nov 20 10:00:00 2024] PHP 8.2.0 Development Server (http://localhost:8000) started
```

---

### 📝 Paso 8.3: Ver la página de bienvenida

1. Abre tu navegador
2. Ve a: `http://localhost:8000`
3. Deberías ver la página con todos los endpoints

---

### 📝 Paso 8.4: Probar con cURL

**Abre OTRA terminal** (deja el servidor corriendo) y ejecuta:

#### 1️⃣ Crear un paciente

```bash
curl -X POST http://localhost:8000/api/pacientes \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "Juan",
    "apellido": "Pérez",
    "email": "juan@email.com",
    "telefono": "555-1234",
    "fecha_nacimiento": "1990-05-15"
  }'
```

**Respuesta esperada:**

```json
{
    "success": true,
    "message": "Paciente creado exitosamente",
    "data": {
        "id": 1,
        "nombre": "Juan",
        "apellido": "Pérez",
        "email": "juan@email.com",
        ...
    }
}
```

#### 2️⃣ Listar pacientes

```bash
curl http://localhost:8000/api/pacientes
```

#### 3️⃣ Ver un paciente específico

```bash
curl http://localhost:8000/api/pacientes/1
```

#### 4️⃣ Crear una cita

```bash
curl -X POST http://localhost:8000/api/citas \
  -H "Content-Type: application/json" \
  -d '{
    "paciente_id": 1,
    "fecha_hora": "2024-12-15 10:00:00",
    "motivo": "Consulta general",
    "estado": "pendiente"
  }'
```

#### 5️⃣ Listar citas

```bash
curl http://localhost:8000/api/citas
```

---

## ✅ Lista de Verificación Final

### Parte 1: Entorno

-   [ ] Creaste todas las carpetas
-   [ ] Creaste composer.json
-   [ ] Ejecutaste `composer install`

### Parte 2: Base de Datos

-   [ ] Creaste Database.php
-   [ ] Entiendes el patrón Singleton

### Parte 3: Repositorios

-   [ ] Creaste PacienteRepository.php (completo)
-   [ ] Creaste CitaRepository.php (completaste los TODOs)
-   [ ] Entiendes qué es un LEFT JOIN

### Parte 4: Controladores

-   [ ] Creaste PacienteController.php (completo)
-   [ ] Creaste CitaController.php (completaste los TODOs)
-   [ ] Entiendes las validaciones

### Parte 5: Router

-   [ ] Creaste public/api.php
-   [ ] Completaste el router de citas
-   [ ] Creaste router.php
-   [ ] Creaste public/index.php

### Parte 6: Pruebas

-   [ ] Iniciaste el servidor
-   [ ] Viste la página de bienvenida
-   [ ] Creaste un paciente con cURL
-   [ ] Listaste pacientes
-   [ ] Creaste una cita
-   [ ] Listaste citas

---

## 🎯 Criterios de Evaluación

| Componente             | Tests | Puntos |
| ---------------------- | ----- | ------ |
| Database & Singleton   | 12    | 18     |
| PacienteRepository     | 10    | 18     |
| CitaRepository         | 9     | 16     |
| Controllers REST       | 18    | 27     |
| Validaciones           | 10    | 18     |
| Integración            | 5     | 9      |
| Estructura Proyecto    | 10    | 18     |
| HTTP Endpoints         | 9     | 26     |
| **Total**              | **83**| **150**|

---

## 🚀 ¿Qué Sigue?

### Mejoras que podrías hacer:

1. **Agregar más validaciones**

    - Validar que la fecha de la cita sea futura
    - Validar que no haya citas duplicadas

2. **Agregar autenticación**

    - Login de usuarios
    - Tokens JWT

3. **Documentar tu API**

    - Crear un README.md con ejemplos
    - Usar Postman para probar

4. **Mejorar la estructura**
    - Agregar middlewares
    - Manejar errores de forma centralizada

---

## 📚 Recursos Adicionales

-   [PHP Manual Oficial](https://www.php.net/manual/es/)
-   [Tutorial de REST APIs](https://restfulapi.net/)
-   [Guía de SQL](https://www.w3schools.com/sql/)
-   [Guía de cURL](https://curl.se/docs/manual.html)

---

## 🎉 ¡Felicitaciones!

Has completado tu primera API REST. Ahora entiendes:

-   ✅ Qué es una API y cómo funciona
-   ✅ Cómo conectarse a una base de datos
-   ✅ Qué es el patrón Repository
-   ✅ Cómo crear endpoints REST
-   ✅ Cómo validar datos
-   ✅ Cómo responder en JSON

**¡Sigue practicando y creando proyectos!** 🚀
