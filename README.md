[![Open in Codespaces](https://classroom.github.com/assets/launch-codespace-2972f46106e565e64193e422d61a12cf1da4916b45550586e14ef0a7c637dd04.svg)](https://classroom.github.com/open-in-codespaces?assignment_repo_id=23465969)
# 🚀 API REST - Sistema de Gestión de Citas Médicas

Bienvenido al repositorio del ejercicio de **API REST** con PHP, SQLite y Programación Orientada a Objetos. Este proyecto te guiará paso a paso para crear una API completa con validación automática mediante tests.

## 📋 Ejercicio Principal: API REST

El ejercicio principal está en **`tasks/ejercicio-api-rest.md`**, donde aprenderás a:

-   ✅ Crear y gestionar bases de datos SQLite
-   ✅ Implementar el patrón Repository
-   ✅ Desarrollar controladores REST
-   ✅ Manejar peticiones HTTP (GET, POST, PUT, DELETE)
-   ✅ Trabajar con formato JSON
-   ✅ Validar datos de entrada

## 📁 Estructura del Proyecto

-   `tasks/ejercicio-api-rest.md` → **📖 EMPIEZA AQUÍ** - Guía completa paso a paso
-   `exercises/api-ejercicio/` → Tu código de la API
-   `tests/Api*.php` → Tests automáticos para validar tu implementación
-   `vendor/` → Dependencias de Composer

## 🚀 Inicio Rápido

### 1. Instalar dependencias (solo una vez)

```bash
composer install
```

### 2. Crear tu API siguiendo la guía

Lee y sigue las instrucciones en `tasks/ejercicio-api-rest.md`

### 3. Ejecutar tests para validar tu código

```bash
# Tests de la API (sin servidor HTTP)
composer test:api

# Tests HTTP (requiere servidor corriendo)
composer test:api-http
```

### 4. Iniciar el servidor de desarrollo

```bash
# Opción 1: Servidor para la API
composer serve:api

# Opción 2: Comando manual
php -S localhost:8000 -t exercises/api-ejercicio/public exercises/api-ejercicio/router.php
```

## 🧪 Tests Disponibles

### Tests de Código (sin servidor)

```bash
# Ejecutar todos los tests de la API
composer test:api
```

**Verifica:**

-   ✅ Clase Database y patrón Singleton
-   ✅ Creación de tablas automática
-   ✅ Repositorios (PacienteRepository, CitaRepository)
-   ✅ Operaciones CRUD completas
-   ✅ Controladores con todos los métodos

### Tests HTTP (requiere servidor corriendo)

**Primero inicia el servidor en una terminal:**

```bash
composer serve:api
```

**Luego en otra terminal ejecuta:**

```bash
composer test:api-http
```

**Verifica:**

-   ✅ Servidor respondiendo
-   ✅ Endpoints GET /api/pacientes
-   ✅ Endpoints POST /api/pacientes
-   ✅ Endpoints GET /api/citas
-   ✅ Manejo de rutas inválidas (404)

> **📝 Nota**: Los tests HTTP han sido ajustados para ser más flexibles y dar mejor retroalimentación. Ver [`exercises/NOTAS-TESTS-HTTP.md`](exercises/NOTAS-TESTS-HTTP.md) para detalles sobre solución de problemas comunes.

## 📝 Comandos Útiles

```bash
# Ver todos los comandos disponibles
composer list

# Regenerar autoload después de crear nuevos archivos
composer dump-autoload

# Ejecutar todos los tests del proyecto
composer test

# Analizar código con PHPStan
composer analyze

# Verificar estilo de código
composer style-check

# Corregir estilo de código
composer style-fix
```

## 📖 Flujo de Trabajo Recomendado

1. **Lee la guía completa** en `tasks/ejercicio-api-rest.md`
2. **Crea los archivos** siguiendo las instrucciones paso a paso
3. **Ejecuta los tests** para verificar tu progreso: `composer test:api`
4. **Inicia el servidor** cuando completes el código: `composer serve:api`
5. **Prueba con cURL** o Postman los endpoints
6. **Ejecuta tests HTTP** para validación final: `composer test:api-http`

## 🎯 Criterios de Evaluación

Tu API será evaluada mediante **tests automáticos** con un total de **150 puntos**:

### Distribución de Puntos por Categoría

-   **Database y Singleton** (12 tests) = 18 puntos

    -   Conexión PDO, patrón Singleton, creación de tablas

-   **Repositorio de Pacientes** (10 tests) = 18 puntos

    -   CRUD completo, validaciones, manejo de datos opcionales

-   **Repositorio de Citas** (9 tests) = 16 puntos

    -   CRUD con LEFT JOIN, relaciones, estado por defecto

-   **Controladores REST** (18 tests) = 27 puntos

    -   Métodos index/show/store/update/destroy, códigos HTTP, JSON

-   **Validaciones** (10 tests) = 18 puntos

    -   Campos requeridos, formato email, estados válidos

-   **Integración** (5 tests) = 9 puntos

    -   Flujos completos, persistencia, actualizaciones

-   **Estructura del Proyecto** (10 tests) = 18 puntos

    -   Directorios, archivos, namespaces, autoload

-   **Endpoints HTTP** (9 tests) = 26 puntos
    -   Servidor funcionando, GET/POST/PUT/DELETE, respuestas JSON

**Total: 83 tests = 150 puntos**

### 📊 Calificación Parcial

✅ **Recibirás puntos por cada test que pase**

-   Cada categoría otorga puntos proporcionales
-   No es todo o nada - cada test cuenta
-   Puedes ver tu progreso en GitHub Actions

## 🆘 Solución de Problemas

### El servidor no inicia

```bash
# Verificar que el puerto 8000 esté libre
lsof -i :8000

# Usar otro puerto
php -S localhost:8080 -t exercises/api-ejercicio/public
```

### Tests fallan por autoload

```bash
cd exercises/api-ejercicio
composer dump-autoload
```

### Base de datos no se crea

Verifica que la carpeta `exercises/api-ejercicio/` tenga permisos de escritura.

## 📚 Recursos Adicionales

-   [PHP Manual Oficial](https://www.php.net/manual/es/)
-   [Tutorial de REST APIs](https://restfulapi.net/)
-   [Guía de PDO (Base de Datos)](https://www.php.net/manual/es/book.pdo.php)
-   [HTTP Status Codes](https://developer.mozilla.org/es/docs/Web/HTTP/Status)

---

## Consejos rápidos

-   Usa `declare(strict_types=1);` en tus archivos PHP
-   Sigue PSR-4 (namespaces y autoloading vía Composer)
-   Implementa las firmas exactamente como se piden en las instrucciones

## Si algo falla

-   Ejecuta `composer dump-autoload` y vuelve a correr los tests
-   Revisa los mensajes de PHPUnit: suelen indicar qué firma o método falta

Más información y recursos están disponibles en los archivos `tasks/` y en la guía del proyecto.

¡Manos a la obra! Sigue las instrucciones en `tasks/` para cada ejercicio y ejecuta los tests
localmente antes de enviar tu entrega.

```bash
 sudo ldconfig
```

Luego:

1. Reinicia la preview de PlantUML: `Alt+D`
2. Si no funciona, recarga la ventana: `Ctrl+Shift+P` → "Reload Window"
3. En último caso, reinicia el codespace completamente

### 🎯 Prevención

El archivo `.devcontainer/setup.sh` instala automáticamente estas dependencias al crear el codespace. Si clonaste el repositorio y el error persiste:

1. Asegúrate de que el script de setup se ejecutó completamente
2. Verifica los logs de creación del codespace
3. Ejecuta manualmente: `bash .devcontainer/setup.sh`

---

✨ **¡Éxito creando tu api de la app de citas medicas.!** ✨

**Recuerda:** Todos los tests deben pasar para validar completamente tu proyecto.
