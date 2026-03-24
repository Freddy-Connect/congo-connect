# Congo Connect

## Vision
Congo Connect is a global platform for the Congolese diaspora.
It combines professional networking, business directory, jobs, training, events, articles, messaging, and subscriptions.

Congo Connect = LinkedIn + Community Directory + Jobs + Training + Events + Media for the Congolese diaspora.

## Main Features
- Professional profiles
- Talent and skills directory
- Companies directory
- Associations directory
- Jobs and applications
- Training and applications
- Events
- Articles
- News feed
- Internal messaging
- Chat
- Media (photos, documents, videos)
- Subscription system with permissions and quotas
- PayPal payments
- Profile PDF generation
- AI features (disabled by default)
- Bilingual (French / English)

## Project Structure
- `legacy/dolphin` → Old Dolphin-based Congo Connect (reference only)
- `database/schema.sql` → New database schema
- `database/init_data.sql` → Initial data
- `public/` → Public web root
- `app/` → Core application logic
- `modules/` → Business modules
- `config/` → Configuration
- `storage/` → Generated files and logs
- `uploads/` → User uploaded files
- `AGENTS.md` → Instructions for Codex
- `README.md` → Project documentation

## Database Installation
1. Create a MySQL database named `congoconnect`.
2. Import `database/schema.sql`.
3. Import `database/init_data.sql`.
4. Create a super admin user manually.

## Development
The application must be built as a modular PHP 8+ application using PDO and MySQL.

The legacy Dolphin system is used only to understand business logic and features.
The new system must be a clean rebuild.