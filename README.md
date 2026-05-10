# Hostify

&#x20;  &#x20;

**Hostify** is a web application for hotel operations management. The system centralizes key processes such as room management, reservations, guests, check-in, check-out, invoicing, payments, shift closing, and housekeeping from an administrative panel built with **Laravel** and **Filament**.

The project is designed for hotels that need to replace manual or scattered processes with an organized, traceable, browser-accessible web platform.

> Note: The application interface is currently in Spanish, as the project is designed for hotel operations in Spanish-speaking environments.

## Demo

Hostify has a deployed version available to test the administrative panel:

[Access the demo](https://hostify-main-jpyutf.free.laravel.cloud/admin/login)

Test credentials:

| Role         | Email                | Password      |
| ------------ | -------------------- | ------------- |
| Super Admin  | `admin@hostify.com`  | `hostify2026` |
| Receptionist | `ana@hostify.com`    | `hostify2026` |
| Housekeeper  | `maria@hostify.com`  | `hostify2026` |
| Supervisor   | `carlos@hostify.com` | `hostify2026` |

## Table of Contents

- [Technologies](#technologies)
- [Overview](#overview)
- [Main Features](#main-features)
- [System Roles](#system-roles)
- [Domain Model](#domain-model)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database](#database)
- [Running Locally](#running-locally)
- [Test Credentials](#test-credentials)
- [Useful Commands](#useful-commands)
- [Project Structure](#project-structure)
- [Author](#author)
- [License](#license)

## Technologies

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

## Overview

Hostify was developed as an administrative solution to support the daily operation of a hotel. Its goal is to improve visibility over room status, organize reservation management, support guest tracking during stays, and keep traceability over operational processes such as housekeeping, payments, and cash shift closing.

The application uses an administrative panel based on Filament, with resources separated by module and access control through roles and permissions. This allows each type of user to access only the features required for their operation.

## Main Features

### Administrative panel

- System access through `/admin`.
- Login for authorized users.
- Administrative panel built with Filament.
- Navigation organized by operational modules.
- Custom visual theme for the panel.
- Redirection from the main route to the administrative panel.

### Rooms

- Room registration and management.
- Association between rooms and room types.
- Management of room number, floor, status, notes, and availability.
- Operational room statuses:
  - Available.
  - Dirty.
  - Occupied.
  - Unavailable.
- Room status changes from the system.
- Status change history including user, previous status, new status, change source, and date.

### Operational room panel

- Visual view to check rooms by status.
- Rooms grouped by floor.
- Operational summary by room status.
- Date filter.
- Periodic panel updates.
- Role-based access.
- Housekeeper-focused view with assigned rooms.

### Room types

- Room type management.
- Relationship between room types and rooms.
- Initial data available through seeders.

### Guests

- Guest registration and management.
- Guest relationship with reservations, invoices, and incidents.
- Centralized guest information for reception operations.

### Reservations

- Reservation creation and management.
- Association between reservations, guests, rooms, and the user who created the record.
- Check-in and check-out date management.
- Room rate registration per reservation.
- Reservation statuses:
  - Pending.
  - Approved.
  - Rejected.
  - Active.
  - Checked out.
  - Cancelled.
- Business actions to approve, reject, cancel, check in, and check out reservations.
- Calculation of nights, room total, additional charges, and invoice total.

### Check-in and check-out

- Check-in for approved reservations.
- Automatic room status change to occupied when check-in is completed.
- Check-out for active reservations.
- Automatic room status change to dirty when check-out is completed.
- Invoice generation during check-out.
- Payment registration associated with check-out.
- Business validations to prevent invalid operations, such as checking out without an open shift or generating duplicate invoices.

### Invoicing and payments

- Invoice generation associated with a reservation.
- System-generated invoice number.
- Registration of subtotal, taxes, total, status, and issue date.
- Payment registration with amount, payment method, responsible user, date, and notes.
- Available payment methods:
  - Cash.
  - Card terminal.
  - Bank transfer.
- Invoice statuses:
  - Draft.
  - Issued.
  - Paid.
  - Cancelled.

### Shift closing

- Shift closing management.
- Association between payments and shift closings.
- Shift closing statuses:
  - Open.
  - Closed.
  - Validated.
- Base structure for cash reconciliation by user and shift.

### Housekeeping

- Housekeeping session management.
- Room assignment to housekeepers.
- Registration of the user who assigns the housekeeping task.
- Management of assigned date, start time, end time, and duration.
- Housekeeping statuses:
  - Pending.
  - In progress.
  - Completed.
- Start and completion of housekeeping sessions from the operational panel.
- Notes and photo upload as evidence after cleaning.
- Automatic room status change to available when housekeeping is completed.

## System Roles

Hostify uses roles and permissions to control access to modules, views, and actions.

Included roles:

- **Super Admin**: full access to the system.
- **Supervisor**: general operational management.
- **Receptionist**: guest, reservation, room, invoicing, and cash operations.
- **Housekeeper**: operational access to the room panel and assigned housekeeping sessions.

## Domain Model

The project is organized around the main entities of hotel operations:

| Entity            | Purpose                                                        |
| ----------------- | -------------------------------------------------------------- |
| `User`            | System users and role assignment.                              |
| `RoomType`        | Available room types in the hotel.                             |
| `Room`            | Physical rooms and operational status.                         |
| `RoomStatusLog`   | Room status change history.                                    |
| `Guest`           | Guest information.                                             |
| `Reservation`     | Reservations associated with guests, rooms, dates, and status. |
| `Invoice`         | Invoices generated from reservations.                          |
| `Charge`          | Additional charges associated with reservations.               |
| `Payment`         | Payments registered during operations.                         |
| `ShiftClose`      | Shift closings and cash control.                               |
| `CleaningSession` | Housekeeping assignments and traceability by room.             |

## Prerequisites

Before installing the project, make sure you have the following installed:

- PHP 8.2 or higher.
- Composer.
- Node.js.
- npm.
- PostgreSQL.
- Git.

Required PHP extensions for a Laravel environment with PostgreSQL:

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

## Installation

Clone the repository:

```bash
git clone https://github.com/CristoferGuillen/Hostify.git
```

Enter the project folder:

```bash
cd Hostify
```

Install PHP dependencies:

```bash
composer install
```

Install JavaScript dependencies:

```bash
npm install
```

Copy the environment file:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

## Configuration

Edit the `.env` file and configure the main application values:

```env
APP_NAME=Hostify
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

Configure the PostgreSQL connection:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hostify
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Create a database named `hostify` before running the migrations.

## Database

Run the migrations:

```bash
php artisan migrate
```

Load the initial data:

```bash
php artisan db:seed
```

You can also reset the database and load seeders in a single command:

```bash
php artisan migrate:fresh --seed
```

## Running Locally

Run the development environment with:

```bash
composer run dev
```

You can also run Laravel and Vite separately.

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Then open the administrative panel at:

```text
http://localhost:8000/admin
```

## Test Credentials

When running the seeders, the project creates initial users to test the main system roles.

| Role         | Email                | Password      |
| ------------ | -------------------- | ------------- |
| Super Admin  | `admin@hostify.com`  | `hostify2026` |
| Receptionist | `ana@hostify.com`    | `hostify2026` |
| Housekeeper  | `maria@hostify.com`  | `hostify2026` |
| Supervisor   | `carlos@hostify.com` | `hostify2026` |

## Useful Commands

Run migrations:

```bash
php artisan migrate
```

Run seeders:

```bash
php artisan db:seed
```

Recreate the database with initial data:

```bash
php artisan migrate:fresh --seed
```

Start the local server:

```bash
php artisan serve
```

Run Vite:

```bash
npm run dev
```

Build assets:

```bash
npm run build
```

Run the complete development environment:

```bash
composer run dev
```

## Project Structure

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

## Author

Developed by **Cristofer Guillen**.

- GitHub: [@CristoferGuillen](https://github.com/CristoferGuillen)
- Repository: [Hostify](https://github.com/CristoferGuillen/Hostify)

## License

This project is available under the **MIT** license.

