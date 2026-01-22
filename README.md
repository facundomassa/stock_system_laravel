# StockSystem

Sistema interno de control de inventario y stock multi-almacén, diseñado para empresas con equipos técnicos en campo.

## Descripción

**StockSystem** es una aplicación web desarrollada en Laravel que permite:

- Gestionar artículos y su stock en múltiples almacenes.
- Realizar movimientos de stock (entradas, salidas, traslados entre almacenes).
- Controlar movimientos en tránsito (pendientes de recepción).
- Generar y registrar remitos.
- Gestionar usuarios con diferentes permisos (administradores, despachantes, técnicos).
- (Próximamente) Módulo para técnicos en calle: solicitar materiales, registrar consumos y validar por despachante.

**Nota importante:** Actualmente el sistema **no maneja valores monetarios** (solo cantidades físicas de stock).

## Tecnologías utilizadas

- **Backend:** Laravel 11 (o 10 – confirmar versión)
- **Frontend:** Blade + Bootstrap 5 + Boxicons
- **Base de datos:** MySQL
- **Otros paquetes:** Laravel Livewire, Spatie Laravel Permission 

## Estado del proyecto

| Módulo                        | Estado                  | Notas |
|-------------------------------|-------------------------|-------|
| Gestión de Artículos          | Funcional               |       |
| Stock por almacén             | Funcional               |       |
| Movimientos (entradas/salidas/traslados) | Funcional     |       |
| Movimientos en tránsito       | Funcional               |  |
| Remitos                       | Funcional               |       |
| Usuarios y permisos           | Funcional               | Usando Spatie Permission |
| Filtro por operación/empresa  | Parcial                 | Falta aplicar scope global |
| Módulo Técnicos (solicitudes y consumos) | Pendiente     | En desarrollo / planificación |
| Reportes básicos              | Parcial               |    Falta implementar reportes complejos |

## Requisitos

- PHP ≥ 8.1
- Composer
- MySQL 8 o MariaDB
- Node.js + NPM (para assets)
- Laravel 10 o 11

## Instalación

1. Clonar el repositorio
   ```Bash
   git clone https://github.com/facundomassa/stock_system_laravel.git
   cd stock_system_laravel
   git checkout main 
   ``` 
2. Instalar dependencias
    ```Bash
    composer install
    npm install
    ```
3. Copiar archivo de entorno    
    ```Bash
    cp .env.example .env
    ```
4. Generar clave de aplicación
    ```Bash
    php artisan key:generate
    ```
5. Configurar la base de datos en .env
6. Ejecutar migraciones y seeders
    ```Bash
    php artisan migrate --seed
    ```
7. Compilar assets
    ```Bash
    npm run dev
    # o para producción:
    npm run build
    ```
8. Iniciar el servidor
    ```Bash
    php artisan serve
    ```
    Acceder en: http://127.0.0.1:8000
    
## Próximos pasos / Roadmap

1. Implementar scope global para filtrar por operación/empresa activa.
2. Desarrollar módulo completo para Técnicos:
    - Solicitud de materiales
    - Registro de consumos
    - Validación por despachante
    - Historial por técnico
3. Mejorar UI/UX (rediseño de movement box, notification box, etc.)
4. Agregar reportes básicos (stock crítico, movimientos por período, etc.)
5. Tests unitarios y de características
6. Documentación técnica detallada (arquitectura, modelos, servicios)