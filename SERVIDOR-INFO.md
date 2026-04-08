# 🚀 Servidor API - Instrucciones de Acceso

## ✅ El servidor está corriendo en el puerto 8080

### 📋 Formas de Acceder

#### 1️⃣ Usando Simple Browser en VSCode (Más fácil)

1. Presiona `Ctrl+Shift+P` (o `Cmd+Shift+P` en Mac)
2. Escribe "Simple Browser"
3. Ingresa la URL: `http://localhost:8080`

#### 2️⃣ Usando el Panel de Puertos (GitHub Codespaces)

1. Busca el panel "PORTS" en la parte inferior de VSCode
2. Encuentra el puerto `8080`
3. Haz clic en el ícono de globo 🌐 para abrir en el navegador
4. O copia la URL pública que aparece

#### 3️⃣ Navegador Externo (Local)

Si estás trabajando localmente (no en contenedor):

-   Abre tu navegador en: http://localhost:8080

### 🎯 URLs Disponibles

-   **📖 Documentación:** `http://localhost:8080/` o `http://localhost:8080/index.php`
-   **🔌 API Endpoint:** `http://localhost:8080/api.php`

### 📚 Ejemplos de Endpoints

```bash
# Listar todos los pacientes
GET http://localhost:8080/api.php?recurso=pacientes

# Obtener un paciente específico
GET http://localhost:8080/api.php?recurso=pacientes&id=1

# Crear un nuevo paciente (con curl)
curl -X POST http://localhost:8080/api.php?recurso=pacientes \
  -H "Content-Type: application/json" \
  -d '{"nombre":"Juan","apellido":"Pérez","email":"juan@example.com"}'

# Listar todas las citas
GET http://localhost:8080/api.php?recurso=citas
```

### 🛠️ Comandos Útiles

```bash
# Ver si el servidor está corriendo
lsof -i :8080

# Detener el servidor
pkill -f "php -S"

# Reiniciar el servidor (desde raíz del proyecto)
php -S 0.0.0.0:8080 -t exercises/api-ejercicio/public

# O usar la tarea de VSCode
# Ctrl+Shift+P -> Tasks: Run Task -> 🎓 Start Exercise API Server
```

### ❓ Solución de Problemas

**Problema:** "No puedo acceder desde el navegador"

-   ✅ Verifica que el servidor use `0.0.0.0` y no `localhost`
-   ✅ Verifica que el puerto 8080 esté abierto
-   ✅ Usa el Simple Browser de VSCode o el panel PORTS

**Problema:** "Error de conexión rechazada"

-   ✅ Verifica que el servidor esté corriendo: `lsof -i :8080`
-   ✅ Reinicia el servidor si es necesario

**Problema:** "404 Not Found"

-   ✅ Verifica que estés usando la URL correcta con `api.php`
-   ✅ Asegúrate de que los archivos estén en `exercises/api-ejercicio/public/`

### 📝 Notas

-   El servidor está configurado para escuchar en `0.0.0.0:8080` para ser accesible en contenedores
-   En GitHub Codespaces, el puerto 8080 se redirige automáticamente a una URL pública
-   En entornos locales, puedes usar `localhost:8080` directamente
-   Los warnings de Xdebug son normales y no afectan la funcionalidad
