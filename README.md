# Hostify

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-4-FFAA00?style=flat-square)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-336791?style=flat-square&logo=postgresql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

<p align="center">
  <a href="https://skillicons.dev">
    <img src="https://skillicons.dev/icons?i=php,laravel,postgres,tailwind,vite,git&theme=light" alt="Tecnologías usadas en Hostify" />
  </a>
</p>

**Hostify** es una aplicación web para la gestión operativa de hoteles. El sistema centraliza procesos clave como la administración de habitaciones, reservas, huéspedes, check-in, check-out, facturación, pagos, cierre de caja por turno y limpieza de habitaciones, todo desde un panel administrativo desarrollado con **Laravel** y **Filament**.

El proyecto está pensado para hoteles que necesitan reemplazar procesos manuales o dispersos por una plataforma web organizada, trazable y accesible desde el navegador.


## Demo

Hostify cuenta con una versión desplegada para probar el panel administrativo:

[Acceder a la demo](https://hostify-main-jpyutf.free.laravel.cloud/admin/login)

Credenciales de prueba:

| Rol           | Email                | Contraseña    |
| ------------- | -------------------- | ------------- |
| Super Admin   | `admin@hostify.com`  | `hostify2026` |
| Recepcionista | `ana@hostify.com`    | `hostify2026` |
| Camarera      | `maria@hostify.com`  | `hostify2026` |
| Supervisor    | `carlos@hostify.com` | `hostify2026` |

## Tabla de contenidos

- [Tecnologías](#tecnologías)
- [Descripción general](#descripción-general)
- [Funcionalidades principales](#funcionalidades-principales)
- [Roles del sistema](#roles-del-sistema)
- [Modelo del dominio](#modelo-del-dominio)
- [Requisitos previos](#requisitos-previos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Base de datos](#base-de-datos)
- [Ejecución local](#ejecución-local)
- [Credenciales de prueba](#credenciales-de-prueba)
- [Comandos útiles](#comandos-útiles)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Autor](#autor)
- [Licencia](#licencia)

## Tecnologías

- **PHP 8.2+**
- **Laravel 12**
- **Filament 4**
- **Filament Shield**
- **Spatie Laravel Permission**
- **PostgreSQL**
- **Blade**
- **Livewire**
- **Vite**
- **Tailwind CSS**

## Descripción general

Hostify fue desarrollado como una solución administrativa para apoyar la operación diaria de un hotel. Su objetivo es mejorar la visibilidad sobre el estado de las habitaciones, organizar la gestión de reservas, facilitar el seguimiento de los huéspedes durante su estadía y mantener trazabilidad sobre procesos operativos como limpieza, pagos y cierres de caja por turno.

La aplicación utiliza un panel administrativo basado en Filament, con módulos separados por área funcional y control de acceso mediante roles y permisos. Esto permite que cada usuario acceda únicamente a las funcionalidades necesarias para su trabajo.

## Funcionalidades principales

### Panel administrativo

- Acceso al sistema desde `/admin`.
- Inicio de sesión para usuarios autorizados.
- Panel administrativo construido con Filament.
- Navegación organizada por módulos operativos.
- Tema visual personalizado para el panel.
- Redirección desde la ruta principal hacia el panel administrativo.

### Habitaciones

- Registro y administración de habitaciones.
- Asociación entre habitaciones y tipos de habitación.
- Gestión de número de habitación, piso, estado, notas y disponibilidad.
- Estados operativos de habitación:
  - Disponible.
  - Sucia.
  - Ocupada.
  - No disponible.
- Cambio de estado de habitaciones desde el sistema.
- Historial de cambios de estado con usuario responsable, estado anterior, nuevo estado, origen del cambio y fecha.

### Panel operativo de habitaciones

- Vista visual para consultar habitaciones según su estado.
- Habitaciones agrupadas por piso.
- Resumen operativo por estado de habitación.
- Filtro por fecha.
- Actualización periódica del panel.
- Acceso controlado por roles.
- Vista enfocada en camareras con habitaciones asignadas.

### Tipos de habitación

- Administración de tipos de habitación.
- Relación entre tipos de habitación y habitaciones.
- Datos iniciales disponibles mediante seeders.

### Huéspedes

- Registro y administración de huéspedes.
- Relación de huéspedes con reservas, facturas e incidentes.
- Información centralizada para apoyar las operaciones de recepción.

### Reservas

- Creación y administración de reservas.
- Asociación entre reservas, huéspedes, habitaciones y usuario que crea el registro.
- Gestión de fechas de check-in y check-out.
- Registro de tarifa por habitación en cada reserva.
- Estados de reserva:
  - Pendiente.
  - Aprobada.
  - Rechazada.
  - Activa.
  - Finalizada.
  - Cancelada.
- Acciones de negocio para aprobar, rechazar, cancelar, realizar check-in y realizar check-out.
- Cálculo de noches, total de habitación, cargos adicionales y total de factura.

### Check-in y check-out

- Check-in para reservas aprobadas.
- Cambio automático del estado de la habitación a ocupada al completar el check-in.
- Check-out para reservas activas.
- Cambio automático del estado de la habitación a sucia al completar el check-out.
- Generación de factura durante el check-out.
- Registro de pago asociado al check-out.
- Validaciones de negocio para evitar operaciones inválidas, como realizar check-out sin un turno de caja abierto o generar facturas duplicadas.

### Facturación y pagos

- Generación de facturas asociadas a reservas.
- Número de factura generado por el sistema.
- Registro de subtotal, impuestos, total, estado y fecha de emisión.
- Registro de pagos con valor, método de pago, usuario responsable, fecha y notas.
- Métodos de pago disponibles:
  - Efectivo.
  - Datáfono.
  - Transferencia bancaria.
- Estados de factura:
  - Borrador.
  - Emitida.
  - Pagada.
  - Cancelada.

### Cierre de caja por turno

- Gestión de cierres de caja por turno.
- Asociación entre pagos y cierres de turno.
- Estados del cierre de turno:
  - Abierto.
  - Cerrado.
  - Validado.
- Estructura base para conciliación de caja por usuario y turno.

### Limpieza de habitaciones

- Gestión de sesiones de limpieza.
- Asignación de habitaciones a camareras.
- Registro del usuario que asigna la tarea de limpieza.
- Gestión de fecha asignada, hora de inicio, hora de finalización y duración.
- Estados de limpieza:
  - Pendiente.
  - En progreso.
  - Completada.
- Inicio y finalización de sesiones de limpieza desde el panel operativo.
- Registro de notas y carga de fotografías como evidencia después de la limpieza.
- Cambio automático del estado de la habitación a disponible cuando se completa la limpieza.

## Roles del sistema

Hostify utiliza roles y permisos para controlar el acceso a módulos, vistas y acciones dentro del sistema.

Roles incluidos:

- **Super Admin**: acceso completo al sistema.
- **Supervisor**: gestión general de la operación.
- **Recepcionista**: gestión de huéspedes, reservas, habitaciones, facturación y caja.
- **Camarera**: acceso operativo al panel de habitaciones y a las sesiones de limpieza asignadas.

## Modelo del dominio

El proyecto está organizado alrededor de las principales entidades de una operación hotelera:

| Entidad           | Propósito                                                            |
| ----------------- | -------------------------------------------------------------------- |
| `User`            | Usuarios del sistema y asignación de roles.                          |
| `RoomType`        | Tipos de habitación disponibles en el hotel.                         |
| `Room`            | Habitaciones físicas y su estado operativo.                          |
| `RoomStatusLog`   | Historial de cambios de estado de las habitaciones.                  |
| `Guest`           | Información de los huéspedes.                                        |
| `Reservation`     | Reservas asociadas a huéspedes, habitaciones, fechas y estados.      |
| `Invoice`         | Facturas generadas a partir de reservas.                             |
| `Charge`          | Cargos adicionales asociados a reservas.                             |
| `Payment`         | Pagos registrados durante la operación.                              |
| `ShiftClose`      | Cierres de turno y control de caja.                                  |
| `CleaningSession` | Asignaciones de limpieza y trazabilidad por habitación.              |

## Requisitos previos

Antes de instalar el proyecto, asegúrate de tener instalado:

- PHP 8.2 o superior.
- Composer.
- Node.js.
- npm.
- PostgreSQL.
- Git.

Extensiones de PHP requeridas para un entorno Laravel con PostgreSQL:

```ini
extension=curl
extension=mbstring
extension=openssl
extension=intl
extension=fileinfo
extension=zip
extension=pdo_pgsql
extension=pgsql
```

## Instalación

Clona el repositorio:

```bash
git clone https://github.com/CristoferGuillen/Hostify.git
```

Entra a la carpeta del proyecto:

```bash
cd Hostify
```

Instala las dependencias de PHP:

```bash
composer install
```

Instala las dependencias de JavaScript:

```bash
npm install
```

Copia el archivo de entorno:

```bash
cp .env.example .env
```

En Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Genera la clave de la aplicación:

```bash
php artisan key:generate
```

## Configuración

Edita el archivo `.env` y configura los valores principales de la aplicación:

```env
APP_NAME=Hostify
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

Configura la conexión a PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hostify
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Antes de ejecutar las migraciones, crea una base de datos llamada `hostify`.

## Base de datos

Ejecuta las migraciones:

```bash
php artisan migrate
```

Carga los datos iniciales:

```bash
php artisan db:seed
```

También puedes reiniciar la base de datos y cargar los seeders con un solo comando:

```bash
php artisan migrate:fresh --seed
```

## Ejecución local

Ejecuta el entorno de desarrollo con:

```bash
composer run dev
```

También puedes ejecutar Laravel y Vite por separado.

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Luego abre el panel administrativo en:

```text
http://localhost:8000/admin
```

## Credenciales de prueba

Al ejecutar los seeders, el proyecto crea usuarios iniciales para probar los roles principales del sistema.

| Rol           | Email                | Contraseña    |
| ------------- | -------------------- | ------------- |
| Super Admin   | `admin@hostify.com`  | `hostify2026` |
| Recepcionista | `ana@hostify.com`    | `hostify2026` |
| Camarera      | `maria@hostify.com`  | `hostify2026` |
| Supervisor    | `carlos@hostify.com` | `hostify2026` |

## Comandos útiles

Ejecutar migraciones:

```bash
php artisan migrate
```

Ejecutar seeders:

```bash
php artisan db:seed
```

Recrear la base de datos con datos iniciales:

```bash
php artisan migrate:fresh --seed
```

Iniciar el servidor local:

```bash
php artisan serve
```

Ejecutar Vite:

```bash
npm run dev
```

Compilar assets:

```bash
npm run build
```

Ejecutar el entorno completo de desarrollo:

```bash
composer run dev
```

## Estructura del proyecto

```text
Hostify/
├── app/
│   ├── Enums/
│   ├── Filament/
│   │   ├── Pages/
│   │   └── Resources/
│   ├── Models/
│   └── Providers/
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── lang/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
├── storage/
├── tests/
├── composer.json
├── package.json
└── vite.config.js
```

## Autor

Desarrollado por **Cristofer Guillen**.

- GitHub: [@CristoferGuillen](https://github.com/CristoferGuillen)
- Repositorio: [Hostify](https://github.com/CristoferGuillen/Hostify)

## Licencia

Este proyecto está disponible bajo la licencia **MIT**.
