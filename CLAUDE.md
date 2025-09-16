# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Development Server
- `composer run dev` - Start development server with concurrent services (Laravel server, queue listener, logs, and Vite)
- `php artisan serve` - Start Laravel development server only
- `php artisan queue:listen --tries=1` - Start queue worker
- `php artisan pail --timeout=0` - View real-time logs
- `npm run dev` - Start Vite development server for assets

### Testing
- `composer run test` - Clear config and run all PHPUnit tests
- `php artisan test` - Run tests directly
- `vendor/bin/phpunit` - Run PHPUnit directly

### Database
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh --seed` - Fresh migrate with seeders
- `php artisan db:seed` - Run database seeders

### Code Quality
- `./vendor/bin/pint` - Run Laravel Pint for code formatting
- `php artisan config:clear` - Clear configuration cache

### Frontend
- `npm run build` - Build production assets with Vite
- `npm run dev` - Development build with hot reload

## Architecture Overview

This is a Laravel 12 employee onboarding API system with the following key components:

### Core Models and Relationships
- **Employee**: Central entity with status tracking (yet_to_start, started, in_progress, success, rejected), offer letter status, division (BEO-India, India-4IT), and category (experienced, fresher, intern)
- **Client**: Associated with employees for client management
- **User**: Authentication model linked to employees
- **Address, Document, Education, Employment**: Related employee data models

### API Structure
- RESTful API routes under `/api` prefix with Sanctum authentication
- Nested resource routes for employee-related data (addresses, documents, educations, employments)
- Authentication endpoints (register, login, logout)
- Client management with email associations

### Key Constants in Employee Model
- Status: YET_TO_START (0), STARTED (1), IN_PROGRESS (2), SUCCESS (3), REJECTED (4)
- Offer Status: PENDING (0), ACCEPTED (1), REJECTED (2)
- Division: BEO_INDIA (0), INDIA_4IT (1)
- Category: EXPERIENCED (0), FRESHER (1), INTERN (2)

### Database
- Uses SQLite for testing (in-memory)
- Migration files follow Laravel conventions
- Seeded with sample data via ClientSeeder and DatabaseSeeder

### Frontend Integration
- Vite for asset compilation with Tailwind CSS 4.0
- Axios for HTTP requests
- Concurrent development setup with Laravel and Vite servers

### Testing Setup
- PHPUnit with Feature and Unit test suites
- SQLite in-memory database for tests
- Configured for array-based cache, mail, and sessions in test environment

## File Structure Patterns
- Controllers in `app/Http/Controllers/Api/` namespace
- Models in `app/Models/` with Eloquent relationships
- API routes in `routes/api.php` with middleware groups
- Migrations in `database/migrations/` with timestamp prefixes
- Request validation classes for form requests