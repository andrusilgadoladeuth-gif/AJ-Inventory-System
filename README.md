# AJ-Inventory-System 📦

Sistema profesional de gestión de inventario y facturación.

## 🚀 Tecnologías utilizadas
* **Backend:** PHP 8.2
* **Database:** MySQL 8.0
* **Infrastructure:** Docker & Docker Compose
* **Frontend:** Bootstrap 5 (Próximamente)


   ## AJ INVENTORY - Sistema de Gestión de Inventario y Ventas
Este es un sistema web ágil desarrollado en PHP para la gestión de inventario, registro de clientes y facturación multi-producto, optimizado para la zona horaria de Colombia.

📋 Características principales
**Gestión de Inventario:** Control de stock y categorías de productos.

**Ventas Multi-producto:** Permite agregar múltiples artículos en una sola transacción.

**Facturación:** Generación de facturas detalladas con impresión amigable.

**Sincronización Horaria:** Configurado específicamente para la hora de Bogotá, Colombia.

**Base de Datos Relacional:** Estructura sólida con integridad referencial (Clientes, Ventas, Productos).

🏗️ Estructura de la Base de Datos
El sistema utiliza 5 tablas principales interconectadas:

1. Categories: Clasificación de productos.

2. Customers: Registro detallado de clientes (NIT/Cédula, Dirección).

3. Products: Catálogo con control de stock.

4. Sales: Cabecera de la transacción (Total y Cliente).

5. Sale_Details: Desglose de cada producto vendido.


## 🛠️ Cómo ejecutar este proyecto
1. Clona el repositorio.
2. Asegúrate de tener **Docker Desktop** y **Visual Studio Code** instalado.
3. Ejecuta el comando:
   ```bash
   docker-compose up -d

Configurar la Base de Datos:

Ingresa a phpMyAdmin desde tu navegador: http://localhost:8081

Crea una base de datos llamada inventory_db (o el nombre que tengas en db.php).

**Servidor:** db
**Usuario:** root
**Contraseña:** root_password

Importa el archivo SQL que se encuentra en la carpeta sql/ o copia el código del esquema directamente en la pestaña SQL.

Acceder al Sistema:

Abre tu navegador y entra a: http://localhost:8085
