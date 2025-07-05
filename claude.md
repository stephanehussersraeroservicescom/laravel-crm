1. Think about the problem first, read the codebase to find the relevant files, and write a plan in tasks/todo.md. 
2. This plan should contain a list of actions to perform that you can check off as they are completed. 
3. Before you begin, contact me so I can verify the plan. 
4. Then start working on the actions to be performed, marking them as completed as you go. 
5. Please clearly explain to me the changes you made at each step. 
6. Simplify every task and code change as much as possible. We want to avoid any massive or complex changes. Each change should have minimal impact on the code. It's all about simplicity. 
7. Finally, add a review section to the [todo.md](http://todo.md/) file with a summary of the changes made and any other relevant information.

TapisCRM - Laravel + Livewire 3 CRM Project
Project Overview
This is a production Laravel CRM system with Livewire 3 hosted at https://www.tapiscrm.xyz. Currently showing default Laravel welcome page, indicating the CRM routes need to be configured as the main application interface.
Key Technology: Livewire 3 for reactive, dynamic frontend without JavaScript frameworks.
Production Status

Live URL: https://www.tapiscrm.xyz
Current State: Default Laravel installation (needs CRM routing configuration)
Environment: Production Ubuntu server
Goal: Deploy functional CRM interface as the main application

Key Features

Customer Management: Store and manage customer information, contacts, and communication history
Lead Tracking: Track potential customers through the sales funnel
Sales Pipeline: Manage deals, opportunities, and sales stages
Contact Management: Organize and maintain contact databases
User Authentication: Secure login system with role-based access
Dashboard & Reporting: Analytics and insights for business performance
Activity Tracking: Log customer interactions and follow-ups

Tech Stack

Backend: Laravel PHP Framework (Latest)
Frontend: Livewire 3 (reactive components, real-time updates)
Styling: Alpine.js + Tailwind CSS (Livewire 3 stack)
Database: MySQL/PostgreSQL
Authentication: Laravel Breeze/Jetstream with Livewire
Real-time: Livewire 3 reactive properties and events
Package Manager: Composer (PHP), NPM (for Tailwind compilation)

Project Structure (Livewire 3 Architecture)
laravel-crm/
├── app/
│   ├── Http/Controllers/     # Minimal controllers (most logic in Livewire)
│   ├── Models/              # Eloquent models (Customer, Lead, Deal, etc.)
│   ├── Livewire/            # Livewire 3 components (main app logic)
│   │   ├── Customer/        # Customer management components
│   │   ├── Lead/           # Lead tracking components
│   │   ├── Dashboard/      # Dashboard widgets
│   │   └── Auth/           # Authentication components
│   ├── Providers/          # Service providers
│   └── Services/           # Business logic services
├── database/
│   ├── migrations/         # Database schema definitions
│   ├── seeders/           # Database seeders
│   └── factories/         # Model factories for testing
├── resources/
│   ├── views/
│   │   ├── layouts/       # Main Livewire layouts
│   │   ├── livewire/      # Livewire component views
│   │   └── components/    # Blade components
│   ├── js/                # Alpine.js and minimal JavaScript
│   └── css/              # Tailwind CSS
├── routes/
│   ├── web.php            # Minimal routes (Livewire handles most)
│   └── api.php            # API routes if needed
└── public/                # Compiled assets
Core Livewire 3 Components

DashboardComponent: Main dashboard with real-time widgets
CustomerListComponent: Interactive customer table with search/filter
CustomerFormComponent: Create/edit customer forms with validation
LeadPipelineComponent: Drag-drop lead management interface
ActivityFeedComponent: Real-time activity notifications
ReportsComponent: Dynamic charts and analytics
UserManagementComponent: User roles and permissions interface
SettingsComponent: System configuration interface

Livewire 3 Features to Implement

Reactive Properties: Real-time data updates without page refresh
Form Validation: Live validation as user types
File Uploads: Drag-drop file handling with progress bars
Modal Components: Dynamic modals for forms and confirmations
Search & Filtering: Live search with debouncing
Pagination: Dynamic pagination without page reload
Bulk Actions: Select multiple items for bulk operations
Notifications: Real-time toast notifications

Production Priorities (URGENT - Livewire 3 Focus)

Livewire 3 Installation: Verify Livewire 3 is properly installed and configured
Route Configuration: Set up Livewire component routes instead of welcome page
Layout Setup: Create main Livewire layout with Alpine.js and Tailwind
Component Structure: Verify Livewire components are properly organized
Database Setup: Ensure production database works with Livewire components
Asset Compilation: Tailwind CSS + Alpine.js compilation for production
Real-time Features: Configure Livewire 3 reactive features
Performance: Livewire 3 optimization and caching strategies

Immediate Code Review Focus (Livewire 3)

Livewire Installation: Check if Livewire 3 is properly installed
Component Architecture: Review Livewire component organization
Route Setup: Livewire routes vs traditional Laravel routes
Layout Configuration: Main layout with Alpine.js and Tailwind
Asset Compilation: Vite configuration for Tailwind + Alpine.js
Component Performance: Lazy loading and optimization
Real-time Features: Reactive properties and event handling
Security: Livewire-specific security considerations

Code Review Priorities

Security: Authentication, authorization, input validation, SQL injection prevention
Performance: Query optimization, caching, lazy loading
Code Quality: Laravel best practices, clean code, proper MVC structure
Database Design: Proper relationships, indexing, migration structure
API Design: RESTful endpoints, proper HTTP status codes, error handling
Frontend: Responsive design, user experience, form validation

Common Issues to Check

 Proper middleware usage for authentication/authorization
 Mass assignment protection in models
 Proper validation rules in form requests
 Database query optimization (N+1 problems)
 Error handling and logging
 API rate limiting and throttling
 Cross-site scripting (XSS) prevention
 CSRF protection implementation

Questions for Review

Are the model relationships properly defined?
Is the authentication system secure and following Laravel best practices?
Are there any performance bottlenecks in the code?
Is the API structure RESTful and well-documented?
Are proper validation rules in place for all user inputs?
Is the database schema optimized for the CRM use case?

Production Environment Details

Server: Ubuntu 2GB NBG1-1 (from your terminal path)
Domain: tapiscrm.xyz with SSL
Web Server: Nginx/Apache (needs verification)
PHP Version: 8.1+ (needs verification)
Laravel Version: Latest (needs verification)
Database: MySQL/PostgreSQL (needs verification)
Node.js: For asset compilation
Current Issue: Default Laravel page instead of CRM interface

Testing

Unit tests for models and services
Feature tests for controllers and APIs
Database tests for migrations and seeders
Integration tests for complete workflows

Deployment

Production environment setup
Environment configuration (.env files)
Database migrations and seeding
Asset compilation and optimization
Security hardening