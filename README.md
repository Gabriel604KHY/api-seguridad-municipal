# API REST de Seguridad, Autenticación y Control de Accesos

## 📋 Descripción del Proyecto
Este proyecto consiste en una API REST modular diseñada para la gestión de usuarios, control de accesos y consumo de servicios en entornos administrativos públicos. Está construida bajo el paradigma de programación Orientada a Objetos (POO) y estructurada bajo principios de Clean Code para garantizar el rendimiento, escalabilidad y la fácil integración de servicios o aplicaciones de terceros.

## 🛠️ Stack Tecnológico Utilizado
* **Framework Backend:** Laravel (PHP)
* **Gestión de Autenticación:** Laravel Sanctum (Tokens de Acceso Bearer / Integridad de Sesión)
* **Motor de Base de Datos:** SQL (Estructura relacional optimizada)
* **Arquitectura:** API RESTful modular

## 🔐 Implementación de Criterios Excluyentes de Seguridad Web
El core de este desarrollo está diseñado para cumplir de manera estricta con las normativas actuales de seguridad web y calidad del software exigidas:

1. **Manejo Seguro de Autenticación y Sesiones (Middleware):** Implementación del sistema de autenticación por tokens criptográficos (Bearer tokens) mediante Laravel Sanctum. Las rutas de lógica de negocio están protegidas por middleware, bloqueando cualquier acceso no autorizado.
2. **Mitigación de Cross-Site Scripting (XSS):** Implementación de capas de sanitización explícita (`strip_tags`) en los controladores para limpiar de forma estricta las entradas de datos de usuarios antes del procesamiento.
3. **Prevención de Inyecciones SQL:** Uso del ORM Eloquent y validaciones robustas mediante tipado estricto a nivel de controlador (`Validator`), asegurando que las sentencias y sublenguajes SQL (DQL, DML, DDL) estén completamente protegidos.

## 🚀 Endpoints de la API
* `POST /api/v1/municipal/registro` - **Público:** Registro seguro de usuarios con emisión automática de token Bearer.
* `GET /api/v1/municipal/indicadores` - **Protegido:** Consumo seguro de un WebService externo (Indicadores económicos chilenos) disponible solo para personal autenticado.
* `POST /api/v1/municipal/tickets` - **Protegido:** Lógica de negocio para el ingreso de solicitudes de soporte vinculadas directamente al ID del usuario autenticado en la sesión.

