# Congo Connect

## Vision

Congo Connect is a global web platform for the Congolese diaspora.

The platform combines professional networking, community directory, jobs, training, events, articles, messaging, and subscription-based services.

Congo Connect can be described as:

LinkedIn + Community Directory + Jobs + Training + Events + Media platform for the Congolese diaspora.

This project is a clean rebuild and does NOT reuse the old Dolphin system code. The legacy Dolphin system is used only as a functional reference.

---

## Main Features

- Professional profiles
- Talent and skills directory
- Companies directory
- Associations directory
- Jobs and job applications
- Training and training applications
- Events management
- Articles and publications
- Central news feed
- Internal messaging
- Chat
- Media (photos, documents, videos)
- Subscription system with permissions and quotas
- PayPal payments
- Profile PDF generation
- AI features (disabled by default, enabled later by admin)
- Bilingual (French / English)

---

## Current Environment Status (Already Configured)

The server environment is already installed and configured.

The following components are already working:

- OVH VPS (Ubuntu)
- Nginx (reverse proxy via Hestia)
- Apache (backend web server)
- PHP 8.3
- MariaDB
- PDO connection to database
- Apache VirtualHost configured
- Nginx → Apache reverse proxy working
- Project root: /var/www/congoconnect
- Public web root: /var/www/congoconnect/public
- public/index.php is working
- Database connection is working

The application already displays:
"Congo Connect fonctionne ! Connexion base de données OK !"

The AI agent must NOT modify server configuration unless explicitly instructed.

---

## Database Status (Already Created)

The database environment is already configured and must NOT be recreated.

- Database name: congoconnect
- Database user: congo_user
- Tables created from: database/schema.sql
- Initial data imported from: database/init_data.sql
- PDO connection configured in: config/database.php

Main tables:

- users
- profiles
- companies
- jobs
- applications

The AI agent must NOT recreate the database or re-import schema unless explicitly instructed.

---

## Project Structure

Project root:

/var/www/congoconnect/

Structure:

- public/ → Public web root (index.php, login.php, register.php, etc.)
- app/ → Core application logic
  - app/controllers → Controllers
  - app/models → Models
  - app/views → Views
  - app/core → Core classes (Router, Controller, Model, Auth, Session)
- modules/ → Business modules (jobs, training, events, messaging, etc.)
- config/ → Configuration files
- database/ → schema.sql, init_data.sql
- storage/ → Logs, cache, generated files
- uploads/ → User uploaded files
- legacy/dolphin/ → Old Dolphin system (reference only)
- README.md → Project documentation
- AGENTS.md → Instructions for AI agents (Codex)

---

## Technical Architecture

Congo Connect is built as a modular PHP application without a framework.

Architecture pattern:

- Front Controller → public/index.php
- MVC (Model - View - Controller)
- Modular architecture for business features
- PDO for database access
- PHP sessions for authentication
- Prepared statements for all SQL queries
- password_hash() and password_verify() for authentication
- Role-based access (member, company, admin)
- Subscription system controlling permissions and quotas

---

## Development Rules

- Use PHP 8+
- Use PDO only (no mysqli)
- Use prepared statements
- Use MVC structure (controllers, models, views)
- Do not put SQL queries inside views
- Keep business logic in controllers and models
- Use password_hash() for passwords
- Use password_verify() for login
- Use PHP sessions for authentication
- Protect pages that require authentication
- Redirect to login if user not authenticated
- Keep code modular and organized
- Do not modify server configuration (Apache/Nginx) unless instructed

---

## Current Development Phase

The infrastructure is complete.
The database is complete.
The PDO connection is working.

The current phase is:

Build the core application:

- User registration
- User login
- User logout
- User session management
- Dashboard
- User profile
- MVC base structure
- Router
- Base Controller
- Base Model
- Auth system

This is the foundation of Congo Connect.
