# Guía de Instalación - E-commerce PHP

## Requisitos del Sistema

- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior
- **Servidor Web**: Apache o Nginx
- **MAMP/XAMPP/WAMP** (recomendado para desarrollo local)

## Instalación Paso a Paso

### 1. Configurar el Servidor Local

#### Opción A: Usando MAMP (macOS)
1. Descarga e instala MAMP desde https://www.mamp.info/
2. Inicia MAMP y configura:
   - Puerto Apache: 80 (o 8888)
   - Puerto MySQL: 3306 (o 8889)
   - Versión PHP: 7.4 o superior

#### Opción B: Usando XAMPP (Windows/Linux/macOS)
1. Descarga e instala XAMPP desde https://www.apachefriends.org/
2. Inicia Apache y MySQL desde el panel de control

### 2. Configurar la Base de Datos

1. **Accede a phpMyAdmin**:
   - MAMP: http://localhost/phpMyAdmin/ o http://localhost:8888/phpMyAdmin/
   - XAMPP: http://localhost/phpmyadmin/

2. **Crear la base de datos**:
   ```sql
   CREATE DATABASE ecommerce_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Importar la estructura**:
   - Selecciona la base de datos `ecommerce_php`
   - Ve a la pestaña "Importar"
   - Selecciona el archivo `install/database.sql`
   - Haz clic en "Continuar"

### 3. Configurar la Aplicación

1. **Configuración ya lista para MAMP**:
   El archivo `install/config.php` ya está configurado para MAMP:
   ```php
   // Configuración de base de datos
   const DB_HOST = '127.0.0.1:8889'; // Puerto MySQL de MAMP
   const DB_NAME = 'ecommerce_php';
   const DB_USER = 'root';            // usuario de MySQL
   const DB_PASS = 'root';            // contraseña por defecto de MAMP
   const DB_CHARSET = 'utf8mb4';
   ```

   **Para XAMPP**, modifica:
   ```php
   const DB_HOST = 'localhost';  // puerto estándar
   const DB_PASS = '';           // sin contraseña
   ```

### 4. Inicializar Datos de Prueba

1. **Ejecutar desde terminal**:
   ```bash
   cd /Applications/MAMP/htdocs/ecommerce-php
   php install/initialize.php
   ```

2. **O ejecutar desde navegador**:
   - Ve a: http://localhost/ecommerce-php/install/initialize.php
   - Esto creará los datos de prueba


## Acceso a la Aplicación

### URLs de Acceso

- **Página Principal**: http://localhost/ecommerce-php/
- **Productos**: http://localhost/ecommerce-php/products
- **Búsqueda**: http://localhost/ecommerce-php/search
- **Calculadora**: http://localhost/ecommerce-php/calculator
- **Contacto**: http://localhost/ecommerce-php/contact
- **Acerca de**: http://localhost/ecommerce-php/about

### Estructura de URLs

- `/` - Página principal
- `/products` - Lista de productos
- `/product/{id}` - Detalle de producto
- `/category/{id}` - Productos por categoría
- `/search?q={término}` - Búsqueda de productos
- `/featured` - Productos destacados
- `/calculator` - Calculadora de cuotas
- `/about` - Acerca de nosotros
- `/contact` - Página de contacto

## Solución de Problemas Comunes

### Error de Conexión a Base de Datos
```
Connection failed: SQLSTATE[HY000] [2002] Connection refused
```
**Solución**:
1. Verifica que MySQL esté ejecutándose en MAMP/XAMPP
2. Confirma el puerto en `install/config.php`
3. Verifica usuario y contraseña

### Error 404 en URLs
**Solución**:
1. Verifica que el archivo `.htaccess` esté en `public_html/`
2. Asegúrate de que Apache tenga habilitado `mod_rewrite`
3. En MAMP: Preferences > Web Server > Apache > Modules > rewrite_module

### Páginas en Blanco
**Solución**:
1. Activa la visualización de errores en `install/config.php`:
   ```php
   const DEBUG_MODE = true;
   ```
2. Revisa los logs de error de Apache
3. Verifica permisos de archivos (755 para directorios, 644 para archivos)

### Error de Autoload
```
Fatal error: Class 'Controllers\HomeController' not found
```
**Solución**:
1. Verifica que todas las clases estén en sus namespaces correctos
2. Confirma que `ClassLoader` esté registrado en `public_html/index.php`

## Características del Sistema

### Funcionalidades Implementadas
- ✅ Catálogo de productos con categorías
- ✅ Sistema de búsqueda y filtros
- ✅ Calculadora de pagos a 6 y 12 meses
- ✅ Comentarios y calificaciones
- ✅ Contador de visitas
- ✅ Productos relacionados
- ✅ Diseño responsivo
- ✅ URLs amigables


## Desarrollo y Personalización

### Estructura de Archivos
```
ecommerce-php/
├── install/          # Scripts de instalación
├── php/             # Lógica de negocio (MVC)
│   ├── Controllers/ # Controladores
│   ├── Models/      # Modelos de datos
│   └── Core/        # Clases base
├── public_html/     # Archivos públicos
│   ├── assets/      # CSS, JS, imágenes
│   ├── views/       # Plantillas PHP
│   └── index.php    # Punto de entrada
└── 
```

### Agregar Nuevas Funcionalidades
1. **Nuevo Controlador**: Crear en `php/Controllers/`
2. **Nuevo Modelo**: Crear en `php/Models/`
3. **Nueva Vista**: Crear en `public_html/views/`
4. **Nueva Ruta**: Agregar en `public_html/index.php`

¡El sistema está listo para usar! 🚀