# 🔧 Taller Técnico - Sistema de Gestión de Órdenes de Servicio

Aplicación web desarrollada en **PHP y MySQL** para la administración de órdenes de reparación y mantenimiento técnico, con sistema de autenticación de usuarios y consulta pública del estado de equipos en tiempo real mediante una **API JSON**.

## 🚀 Características Principales
* **Autenticación y Control de Sesiones:** Inicio y cierre de sesión seguro (`login.php` y `logout.php`) para proteger el acceso administrativo.
* **Panel Administrativo (`panel.php`):** Gestión de órdenes de servicio técnico, seguimiento de estados y control de clientes.
* **Consulta en Tiempo Real (`api_estado.php`):** Endpoint que devuelve respuestas en formato **JSON** para consultar el estado de una orden sin recargar la página.
* **Base de Datos Relacional (`taller_tecnico.sql`):** Conexión segura a MySQL utilizando **PDO** para prevenir inyecciones SQL.

## 🛠️ Tecnologías Utilizadas
* **Backend:** PHP (PDO)
* **Base de Datos:** MySQL / phpMyAdmin
* **Frontend:** HTML5, CSS3, JavaScript
* **Formato de intercambio:** JSON (API REST básica)
* **Servidor Local:** Apache (XAMPP)

## ⚙️ Instalación y Uso Local
1. Clona o descarga este repositorio en la carpeta `htdocs` de tu servidor local (XAMPP/WAMP).
2. Abre **phpMyAdmin**, crea una base de datos llamada `taller_tecnico` e importa el archivo `taller_tecnico.sql`.
3. Verifica que las credenciales de conexión en el archivo `conexion.php` coincidan con tu entorno local (`usuario`, `contraseña` y `nombre de la base de datos`).
4. Abre tu navegador e ingresa a `http://localhost/TallerTecnico/index.php`.

---
👨‍💻 **Desarrollado por:** John Fredy Velásquez Cadavid — Estudiante de Análisis y Desarrollo de Software (ADSO - SENA)
