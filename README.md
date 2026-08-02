# API REST de Seguridad, Autenticación y Control de Usuarios

## 📋 Descripción del Proyecto
Este proyecto consiste en una API REST modular e independiente diseñada para la gestión de usuarios y control de accesos en entornos administrativos públicos. Está construida bajo el paradigma de desarrollo Orientado a Objetos (OO) y estructurada bajo principios de Clean Code para garantizar el rendimiento, escalabilidad y la fácil integración de servicios o aplicaciones de terceros.

## 🛠️ Stack Tecnológico Utilizado
* **Framework Backend:** Laravel (PHP)
* **Gestión de Autenticación:** Laravel Sanctum (Tokens de Acceso Bearer / Integridad de Sesión)
* **Motor de Base de Datos:** SQL (Estructura relacional optimizada)
* **Arquitectura:** API RESTful

## 🔐 Implementación de Criterios Excluyentes de Seguridad Web
El core de este desarrollo está diseñado para cumplir de manera estricta con las normativas actuales de seguridad web y calidad del software:

1. **Mitigación de Cross-Site Scripting (XSS):** Implementación de capas de sanitización explícita (`strip_tags`) en los controladores para limpiar entradas de datos de usuarios antes del procesamiento.
2. **Prevención de Inyecciones SQL y SQLi:** Uso del ORM Eloquent y validaciones robustas mediante tipado estricto a nivel de controlador (`Validator`), asegurando que las sentencias y sublenguajes SQL (DQL, DML, DDL) estén completamente protegidos.
3. **Manejo Seguro de Autenticación y Sesiones:** Control estricto de acceso mediante autenticación basada en tokens criptográficos seguros (Bearer tokens), protegiendo rutas sensibles de la API contra accesos no autorizados.
4. **Integridad y Hash de Datos:** Almacenamiento seguro de credenciales críticas mediante algoritmos de encriptación hash unidireccionales de alto rendimiento.

## 🚀 Endpoints de la API
* `POST /api/v1/municipal/registro` - Registro seguro y sanitizado de cuentas de usuario con emisión automática de token Bearer.

