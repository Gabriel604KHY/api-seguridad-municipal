# API REST de Seguridad y Gestión Municipal

## Descripción

API REST desarrollada con Laravel para gestionar autenticación, control de acceso por roles y solicitudes de soporte municipal.

El proyecto utiliza Laravel Sanctum para emitir tokens Bearer, proteger rutas privadas e identificar al usuario que realiza cada operación.

Los tickets se almacenan en una base de datos relacional y se asocian automáticamente al usuario autenticado, evitando que el cliente pueda seleccionar manualmente otro `user_id`.

## Tecnologías utilizadas

- PHP 8.2
- Laravel
- Laravel Sanctum
- Eloquent ORM
- Base de datos relacional
- API REST
- Git y GitHub

## Funcionalidades implementadas

- Registro de usuarios.
- Inicio de sesión.
- Generación de tokens Bearer.
- Consulta del usuario autenticado.
- Cierre de sesión y revocación del token actual.
- Protección de rutas mediante `auth:sanctum`.
- Control de acceso por roles.
- Roles de administrador, supervisor y operador.
- Creación y almacenamiento de tickets municipales.
- Asociación automática entre tickets y usuarios.
- Validación de datos recibidos.
- Consumo de un servicio externo de indicadores económicos.
- Respuestas en formato JSON.

## Roles disponibles

| Rol | Descripción |
|---|---|
| `administrador` | Acceso completo a las funciones administrativas habilitadas |
| `supervisor` | Acceso a indicadores y operaciones de supervisión |
| `operador` | Acceso a la creación de tickets municipales |

Los usuarios registrados reciben por defecto el rol:

```text
operador
```

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

Este middleware comprueba que el usuario autenticado tenga uno de los roles permitidos para acceder a la ruta.

Ejemplo:

```php
->middleware('rol:administrador,supervisor')
```

### Protección de contraseñas

Las contraseñas se almacenan mediante el sistema de hashing proporcionado por Laravel.

Durante el inicio de sesión, la contraseña enviada se compara con el hash almacenado mediante `Hash::check()`.

### Protección frente a inyección SQL

Las operaciones con la base de datos se realizan mediante Eloquent ORM y consultas parametrizadas por Laravel.

Además, los datos se validan antes de ser procesados o almacenados.

### Validación y normalización

La API valida campos obligatorios, formatos, tamaños máximos y valores permitidos.

También se normalizan campos como correos electrónicos, nombres, títulos y descripciones antes de guardarlos.

### Asociación segura de tickets

El campo `user_id` no se recibe desde el cliente.

El usuario propietario del ticket se obtiene directamente desde el token de Sanctum:

```php
$usuario = $request->user();
$ticket->user_id = $usuario->id;
```

De esta manera, un usuario no puede crear un ticket utilizando el identificador de otro usuario.

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
| `GET` | `/indicadores` | Administrador, supervisor | Consulta indicadores económicos |
| `POST` | `/tickets` | Administrador, supervisor, operador | Crea y almacena un ticket |
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
    "email": "usuario@municipal.cl"
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

## Consultar usuario autenticado

```http
GET /api/v1/municipal/usuario
Accept: application/json
Authorization: Bearer TOKEN
```

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

No se debe enviar `user_id`. La API utiliza automáticamente el usuario autenticado.

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

## Instalación

Clonar el repositorio:

```bash
git clone https://github.com/Gabriel604KHY/api-seguridad-municipal.git
cd api-seguridad-municipal
```

Instalar las dependencias:

```bash
composer install
```

Crear el archivo de entorno:

```bash
copy .env.example .env
```

En Linux o macOS:

```bash
cp .env.example .env
```

Generar la clave de Laravel:

```bash
php artisan key:generate
```

Configurar la conexión a la base de datos en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_seguridad_municipal
DB_USERNAME=root
DB_PASSWORD=
```

Ejecutar las migraciones:

```bash
php artisan migrate
```

Iniciar el servidor:

```bash
php artisan serve
```

La API estará disponible en:

```text
http://127.0.0.1:8000
```

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

Comprobar las rutas:

```bash
php artisan route:list --path=api
```

Limpiar la caché de Laravel:

```bash
php artisan optimize:clear
```

Verificar la sintaxis de un archivo PHP:

```bash
php -l ruta/del/archivo.php
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

database/
└── migrations/

routes/
└── api.php
```

## Estado actual

Esta versión permite registrar usuarios, iniciar y cerrar sesión, controlar accesos por roles y crear tickets persistentes.

Entre las mejoras futuras se consideran:

- Listado de tickets.
- Consulta de un ticket por ID.
- Actualización del estado de un ticket.
- Asignación de tickets a operadores.
- Filtros y paginación.
- Form Requests.
- Pruebas automatizadas.
- Documentación con Swagger u OpenAPI.
- Registro de auditoría.

## Autor

**Gabriel Saldías**

GitHub: `Gabriel604KHY`