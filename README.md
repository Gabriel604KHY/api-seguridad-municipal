# API REST de Seguridad y Gestión Municipal

## Descripción

API REST desarrollada con Laravel para gestionar autenticación, control de acceso por roles, tickets municipales e integración con servicios externos.

El proyecto utiliza Laravel Sanctum para emitir tokens Bearer, proteger rutas privadas e identificar al usuario que realiza cada operación.

Los tickets se almacenan en una base de datos relacional y se asocian automáticamente al usuario autenticado, evitando que el cliente pueda seleccionar manualmente otro `user_id`.

Esta API es utilizada por el frontend:

```text
https://github.com/Gabriel604KHY/Panel-tickets-municipales
```

## Tecnologías utilizadas

- PHP 8.2
- Laravel
- Laravel Sanctum
- Eloquent ORM
- MySQL
- API REST
- Consumo de WebServices externos
- Git y GitHub

## Funcionalidades implementadas

- Registro de usuarios.
- Inicio de sesión.
- Generación de tokens Bearer.
- Consulta del usuario autenticado.
- Cierre de sesión y revocación del token actual.
- Protección de rutas mediante `auth:sanctum`.
- Control de acceso mediante roles.
- Roles de administrador, supervisor y operador.
- Middleware personalizado para autorización.
- Creación y almacenamiento de tickets municipales.
- Asociación automática entre tickets y usuarios.
- Listado de tickets del usuario autenticado.
- Búsqueda por título, descripción o ubicación.
- Filtros por estado, prioridad y categoría.
- Paginación de resultados.
- Validación y normalización de datos.
- Consumo de indicadores económicos desde una API externa.
- Respuestas estructuradas en formato JSON.

## Roles disponibles

| Rol | Permisos |
|---|---|
| `administrador` | Tickets, indicadores y rutas administrativas |
| `supervisor` | Tickets e indicadores económicos |
| `operador` | Creación y consulta de tickets |

Los usuarios registrados reciben por defecto el rol:

```text
operador
```

El cliente no puede seleccionar libremente su rol durante el registro.

## Seguridad implementada

### Autenticación con Laravel Sanctum

La API utiliza tokens de acceso personales de Laravel Sanctum.

Las rutas privadas requieren el encabezado:

```http
Authorization: Bearer TOKEN
```

### Control de acceso por roles

Se implementó el middleware personalizado:

```text
VerificarRol
```

Este middleware comprueba que el usuario autenticado tenga uno de los roles permitidos.

Ejemplo:

```php
->middleware('rol:administrador,supervisor')
```

Si el usuario no está autenticado, la API devuelve un estado `401`.

Si está autenticado, pero no posee un rol autorizado, devuelve un estado `403`.

### Protección de contraseñas

Las contraseñas se almacenan utilizando el sistema de hashing de Laravel.

Durante el inicio de sesión se utiliza:

```php
Hash::check($password, $usuario->password);
```

Las contraseñas nunca son devueltas en las respuestas JSON.

### Protección frente a inyección SQL

Las operaciones de base de datos se realizan mediante Eloquent ORM y consultas parametrizadas por Laravel.

Los valores recibidos también son validados antes de utilizarse en consultas o procesos de persistencia.

### Validación y normalización

La API valida:

- Campos obligatorios.
- Formatos de correo electrónico.
- Longitudes mínimas y máximas.
- Valores permitidos para estados y prioridades.
- Parámetros de filtros y paginación.

También se normalizan campos como:

- Nombre.
- Correo electrónico.
- Título.
- Descripción.
- Categoría.
- Prioridad.
- Ubicación.

### Asociación segura de tickets

El campo `user_id` no se recibe desde el cliente.

El propietario del ticket se obtiene directamente desde el usuario autenticado:

```php
$usuario = $request->user();

$ticket->user_id = $usuario->id;
```

De esta manera, un usuario no puede crear un ticket utilizando el identificador de otro usuario.

### Verificación TLS

La conexión con el servicio externo de indicadores mantiene habilitada la verificación de certificados TLS.

No se utiliza `withoutVerifying()`.

## Endpoints

URL base local:

```text
http://127.0.0.1:8000/api/v1/municipal
```

### Rutas públicas

| Método | Endpoint | Descripción |
|---|---|---|
| `POST` | `/registro` | Registra un usuario y genera un token |
| `POST` | `/login` | Inicia sesión y genera un token |

### Rutas autenticadas

| Método | Endpoint | Roles permitidos | Descripción |
|---|---|---|---|
| `GET` | `/usuario` | Usuario autenticado | Obtiene los datos del usuario actual |
| `POST` | `/logout` | Usuario autenticado | Revoca el token utilizado |
| `GET` | `/tickets` | Administrador, supervisor, operador | Lista y filtra los tickets del usuario |
| `POST` | `/tickets` | Administrador, supervisor, operador | Crea y almacena un ticket |
| `GET` | `/indicadores` | Administrador, supervisor | Consulta indicadores económicos |
| `GET` | `/admin/verificar` | Administrador | Comprueba el acceso administrativo |

## Registro de usuario

### Solicitud

```http
POST /api/v1/municipal/registro
Content-Type: application/json
Accept: application/json
```

```json
{
  "name": "Usuario Municipal",
  "email": "usuario@municipal.cl",
  "password": "Municipal123",
  "device_name": "Postman"
}
```

### Respuesta esperada

```json
{
  "mensaje": "Usuario registrado correctamente.",
  "usuario": {
    "id": 1,
    "name": "Usuario Municipal",
    "email": "usuario@municipal.cl",
    "role": "operador"
  },
  "access_token": "TOKEN_GENERADO",
  "token_type": "Bearer"
}
```

## Inicio de sesión

### Solicitud

```http
POST /api/v1/municipal/login
Content-Type: application/json
Accept: application/json
```

```json
{
  "email": "usuario@municipal.cl",
  "password": "Municipal123",
  "device_name": "Postman"
}
```

### Respuesta esperada

```json
{
  "mensaje": "Inicio de sesión exitoso.",
  "usuario": {
    "id": 1,
    "name": "Usuario Municipal",
    "email": "usuario@municipal.cl",
    "role": "operador"
  },
  "access_token": "TOKEN_GENERADO",
  "token_type": "Bearer"
}
```

## Consultar usuario autenticado

```http
GET /api/v1/municipal/usuario
Accept: application/json
Authorization: Bearer TOKEN
```

La respuesta incluye:

- Identificador.
- Nombre.
- Correo electrónico.
- Rol.
- Fecha de creación.
- Fecha de actualización.

## Cerrar sesión

```http
POST /api/v1/municipal/logout
Accept: application/json
Authorization: Bearer TOKEN
```

La operación elimina el token utilizado en la solicitud.

## Crear un ticket

### Solicitud

```http
POST /api/v1/municipal/tickets
Content-Type: application/json
Accept: application/json
Authorization: Bearer TOKEN
```

```json
{
  "titulo": "Luminaria pública apagada",
  "descripcion": "La luminaria ubicada frente a la plaza no enciende durante la noche.",
  "categoria": "alumbrado",
  "prioridad": "alta",
  "ubicacion": "Plaza principal, sector norte"
}
```

No se debe enviar `user_id`. La API utiliza automáticamente al usuario autenticado.

### Prioridades permitidas

```text
baja
media
alta
critica
```

### Estado inicial

Todos los tickets nuevos se crean con el estado:

```text
abierto
```

## Listar tickets

```http
GET /api/v1/municipal/tickets
Accept: application/json
Authorization: Bearer TOKEN
```

La API devuelve únicamente los tickets asociados al usuario autenticado.

### Parámetros de consulta

| Parámetro | Descripción |
|---|---|
| `buscar` | Busca por título, descripción o ubicación |
| `estado` | Filtra por estado |
| `prioridad` | Filtra por prioridad |
| `categoria` | Filtra por categoría |
| `per_page` | Cantidad de registros por página, entre 1 y 50 |

Ejemplo:

```http
GET /api/v1/municipal/tickets?estado=abierto&prioridad=alta&buscar=luminaria
```

### Estados permitidos

```text
abierto
en_proceso
resuelto
cerrado
```

### Respuesta esperada

```json
{
  "mensaje": "Tickets obtenidos correctamente.",
  "tickets": [
    {
      "id": 1,
      "user_id": 1,
      "titulo": "Luminaria pública apagada",
      "descripcion": "La luminaria no enciende durante la noche.",
      "categoria": "alumbrado",
      "prioridad": "alta",
      "estado": "abierto",
      "ubicacion": "Plaza principal",
      "created_at": "2026-08-03T23:00:00.000000Z",
      "updated_at": "2026-08-03T23:00:00.000000Z"
    }
  ],
  "meta": {
    "pagina_actual": 1,
    "ultima_pagina": 1,
    "por_pagina": 10,
    "total": 1
  }
}
```

## Indicadores económicos

```http
GET /api/v1/municipal/indicadores
Accept: application/json
Authorization: Bearer TOKEN
```

Disponible para:

```text
administrador
supervisor
```

La API consulta el servicio externo:

```text
https://mindicador.cl/api
```

Indicadores entregados:

- UF.
- UTM.
- Dólar observado.
- Euro.

### Respuesta esperada

```json
{
  "mensaje": "Indicadores económicos obtenidos correctamente.",
  "origen": "mindicador.cl",
  "indicadores": {
    "uf": 39240.12,
    "utm": 68785,
    "dolar": 963.45,
    "euro": 1108.72
  }
}
```

Los valores del ejemplo son referenciales y cambian según la respuesta del servicio externo.

## Instalación

### Clonar el repositorio

```bash
git clone https://github.com/Gabriel604KHY/api-seguridad-municipal.git
cd api-seguridad-municipal
```

### Instalar dependencias

```bash
composer install
```

### Crear el archivo de entorno

En Windows:

```bash
copy .env.example .env
```

En Linux o macOS:

```bash
cp .env.example .env
```

### Generar la clave de Laravel

```bash
php artisan key:generate
```

### Configurar la base de datos

Ejemplo para MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_seguridad_municipal
DB_USERNAME=root
DB_PASSWORD=
```

### Ejecutar las migraciones

```bash
php artisan migrate
```

### Iniciar el servidor

```bash
php artisan serve
```

La API estará disponible en:

```text
http://127.0.0.1:8000
```

## Configuración de certificados en XAMPP

Para consumir servicios HTTPS, PHP debe tener configurado un archivo de certificados CA.

Ejemplo en `php.ini`:

```ini
curl.cainfo="C:\xampp\php\extras\ssl\cacert.pem"
openssl.cafile="C:\xampp\php\extras\ssl\cacert.pem"
```

Comprobar la configuración:

```bash
php -r "echo ini_get('curl.cainfo'), PHP_EOL; echo ini_get('openssl.cafile'), PHP_EOL;"
```

No se debe desactivar la validación TLS en el código de producción.

## Migraciones principales

El proyecto contiene migraciones para:

- Usuarios.
- Caché y trabajos en segundo plano.
- Tokens personales de Sanctum.
- Campo de rol para usuarios.
- Tickets municipales.

Para comprobar su estado:

```bash
php artisan migrate:status
```

## Comandos de verificación

Comprobar rutas:

```bash
php artisan route:list --path=api
```

Limpiar la caché:

```bash
php artisan optimize:clear
```

Verificar la sintaxis de un archivo:

```bash
php -l ruta/del/archivo.php
```

Ejecutar la consola interactiva:

```bash
php artisan tinker
```

## Estructura principal

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthApiController.php
│   │   ├── MunicipalServiceController.php
│   │   └── TicketApiController.php
│   └── Middleware/
│       └── VerificarRol.php
├── Models/
│   ├── Ticket.php
│   └── User.php

bootstrap/
└── app.php

database/
└── migrations/

routes/
└── api.php
```

## Conceptos técnicos demostrados

- Programación orientada a objetos.
- Controladores, modelos y middleware.
- Consultas DQL mediante Eloquent.
- Inserciones y actualizaciones DML.
- Definición de tablas DDL mediante migraciones.
- Autenticación mediante tokens.
- Autorización basada en roles.
- Validación y normalización de entradas.
- Relaciones entre tablas.
- Paginación.
- Consumo de APIs externas.
- Manejo de errores HTTP.
- Control de versiones con Git y GitHub.

## Estado actual

Actualmente se encuentran implementados:

- Registro e inicio de sesión.
- Tokens Bearer.
- Cierre de sesión.
- Consulta del usuario autenticado.
- Roles y permisos.
- Creación persistente de tickets.
- Listado de tickets.
- Filtros de búsqueda.
- Paginación.
- Indicadores económicos.
- Integración con el frontend React y TypeScript.

## Mejoras futuras

- Consulta individual de un ticket.
- Actualización del estado.
- Asignación de tickets a operadores.
- Historial de cambios.
- Form Requests.
- Transacciones SQL.
- Registro de auditoría.
- Pruebas automatizadas.
- Documentación con Swagger u OpenAPI.
- Recuperación de contraseña.
- Despliegue en un entorno público.

## Autor

**Gabriel Saldías**

GitHub:

```text
Gabriel604KHY
```