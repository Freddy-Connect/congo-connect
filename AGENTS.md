# AGENTS.md — Congo Connect

## Project Overview
Congo Connect is a global web platform for the Congolese diaspora.
It combines professional networking, community directory, jobs, training, events, articles, messaging, and subscriptions.

This project is being rebuilt from a legacy Dolphin (Boonex) platform into a modern PHP 8+ application.

## Source of Truth
The source of truth for the new system is:
- database/schema.sql
- database/init_data.sql
- README.md
- This AGENTS.md file

The legacy Dolphin codebase in /legacy/dolphin is for reference only.

## Legacy Dolphin
There is a legacy Dolphin-based version of Congo Connect in /legacy/dolphin.

Important rules:
- Do NOT migrate Dolphin directly.
- Do NOT try to make Dolphin compatible with PHP 8.
- Use Dolphin only to:
  - understand business logic
  - understand modules
  - understand database relationships
  - understand user flows
  - understand memberships and permissions
  - understand messaging, media, jobs, events, articles

The new application must be a clean, modern rebuild.

## Core Modules
The platform is modular. Main modules:
- Authentication & Users
- Profiles
- Talent Directory
- Companies
- Associations
- Jobs
- Trainings
- Events
- Articles
- News Feed
- Media (photos, documents, videos)
- Messaging
- Chat
- Subscriptions (permissions & quotas)
- Payments (PayPal)
- Notifications
- PDF Generation
- AI (disabled by default)
- Admin

## Key Business Rules
- Users apply to jobs using their Congo Connect profile, not by uploading a CV manually.
- The system can generate a PDF version of the profile for job applications.
- A snapshot of the profile must be stored at the time of application.
- The talent directory and professional directory are combined into one module.
- Media (photos, documents, videos) is a shared module used across the platform.
- The news feed is a central feature.
- The platform must be bilingual: French and English.
- AI features must be implemented but disabled by default and can be enabled by admin.
- Subscriptions control permissions and quotas.
- If a quota value is NULL, it means unlimited.

## Technical Stack
- Linux
- Apache
- MySQL 8+
- PHP 8+
- HTML / CSS / JavaScript
- PDO for database access
- PayPal for payments

## Architecture Rules
- Use a modular structure in /modules
- Shared logic goes into /app
- Public entry point is /public/index.php
- Configuration files go into /config
- Uploaded files go into /uploads
- Generated files and logs go into /storage

## Development Order
1. Authentication
2. Profiles
3. Media upload
4. News Feed
5. Talent Directory
6. Companies
7. Jobs
8. Trainings
9. Events
10. Articles
11. Messaging
12. Chat
13. Subscriptions & Permissions
14. Payments
15. PDF Generation
16. AI Features
17. Admin Panel

## Coding Guidelines
- Use PDO, not deprecated MySQL functions.
- Use prepared statements.
- Validate all user inputs.
- Implement role-based access control.
- Implement subscription-based permission checks.
- Write clean, readable, modular code.

## Environment Status (Already Completed)

The server environment is already configured and working:

- VPS OVH configured
- Nginx reverse proxy configured
- Apache configured
- PHP 8.3 installed
- MariaDB installed
- Database `congoconnect` already created
- Tables already imported
- PDO connection already working
- public/index.php already working

The AI agent must focus only on application development, not server configuration.
Do NOT modify server configuration, Apache, Nginx, or database structure unless explicitly instructed.
