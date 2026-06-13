# Sistema de Gestión para ventas de piezas 3D

Hola, mi nombre es Nicolás Zamora y este proyecto es una aplicación web desarrollada en PHP para la gestión de ventas de piezas impresas en 3D.

## Fase: **Beta**

## Funcionalidades
- Página de inicio con registro e inicio de sesión
- Autenticación segura (`password_hash`, `password_verify`, `session_regenerate_id`)
- Protección CSRF en todos los formularios
- Sistema de roles con 4 perfiles diferenciados
- CRUD completo de productos/piezas 3D (solo admin)
- Sistema de pedidos con estados de producción

## Tecnologías
- PHP
- Bootstrap 5
- MySQL
- JavaScript (JS)
- CSS

---

## Sistema de Roles

Al iniciar sesión, cada usuario es redirigido automáticamente a su interfaz correspondiente.

### Administrador (`admin`)
Acceso total al sistema.
- Panel con estadísticas globales (usuarios, productos, pedidos por estado)
- Gestión de usuarios: listado y cambio de rol con descripción visual
- CRUD completo de productos
- Acceso a todas las vistas de vendedor y operario

### Vendedor (`vendedor`)
Gestiona las ventas y pedidos.
- Panel con resumen de sus propias ventas
- Crear pedidos para cualquier cliente registrado (seleccionando cliente + productos + cantidades)
- Ver todos los pedidos del sistema
- Cancelar pedidos en estado `pendiente`

### Operario (`operario`)
Gestiona la producción y el stock.
- Panel con alertas de stock bajo (≤ 5 unidades) y pedidos activos
- Cola de producción: cambia el estado de los pedidos (`pendiente` → `en proceso` → `completado`)
- Actualización de stock por producto

### Cliente (`cliente`)
Realiza y consulta sus propios pedidos.
- Panel de bienvenida con resumen de su historial
- Catálogo de piezas disponibles con filtro por categoría
- Realizar pedidos directamente (selección de productos + cantidades + notas)
- Ver el estado de sus pedidos en tiempo real

---

## Estructura del proyecto

```
PrintStock-3d-main/
├── assets/
│   ├── css/style.css
│   └── js/main.js
├── config/
│   ├── app.php             # BASE_URL y control de errores (DEBUG)
│   ├── database.php        # Conexión PDO a MySQL
│   └── schema.sql          # SQL para crear todas las tablas
├── includes/
│   ├── auth.php            # requireAuth, requireRole, hasRole, csrfToken, estadoBadge, rolBadge
│   ├── header.php          # Navbar dinámico según rol
│   └── footer.php          # Cierre HTML
├── pages/
│   ├── admin/
│   │   ├── index.php           # Dashboard admin
│   │   ├── usuarios.php        # Listado de usuarios
│   │   └── usuario_rol.php     # Cambiar rol de usuario
│   ├── vendedor/
│   │   ├── index.php           # Dashboard vendedor
│   │   ├── pedidos.php         # Ver y cancelar pedidos
│   │   └── pedido_nuevo.php    # Crear pedido para cliente
│   ├── operario/
│   │   ├── index.php           # Dashboard operario
│   │   ├── produccion.php      # Cola de producción + cambio de estado
│   │   └── stock.php           # Actualizar stock de productos
│   ├── cliente/
│   │   ├── index.php           # Dashboard cliente
│   │   ├── catalogo.php        # Catálogo con filtro por categoría
│   │   ├── mis_pedidos.php     # Historial de pedidos propios
│   │   └── pedido_nuevo.php    # Hacer un pedido
│   ├── productos.php           # CRUD de productos (solo admin)
│   ├── producto_nuevo.php      # Crear producto (solo admin)
│   ├── producto_editar.php     # Editar producto (solo admin)
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php           # Router: redirige según rol
│   └── logout.php
├── index.php
└── test.php
```

---

## Instalación

1. Crear la base de datos `mi_web` en MySQL.
2. Importar `config/schema.sql`.
3. Ajustar credenciales en `config/database.php` si es necesario.
4. Registrar el primer usuario y luego asignarle rol admin manualmente:
   ```sql
   UPDATE usuarios SET rol = 'admin' WHERE id = 1;
   ```
5. Si el proyecto está en un subdirectorio del servidor, cambiar `BASE_URL` en `config/app.php`.
6. Para producción, cambiar `define('DEBUG', false)` en `config/app.php`.

## Tablas de la base de datos

| Tabla          | Descripción                              |
|----------------|------------------------------------------|
| `usuarios`     | Usuarios con campo `rol` ENUM            |
| `productos`    | Catálogo de piezas 3D                    |
| `pedidos`      | Pedidos con cliente, vendedor y estado   |
| `pedido_items` | Detalle de productos por pedido          |
