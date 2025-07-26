# Book Lending System API

A comprehensive **Book Lending System** built with Laravel 12 and PHP 8.4, featuring REST API architecture with Redis-based concurrency control and extensive testing coverage.

> **Note:** This project also serves as a technical demonstration for Laravel backend developer assessment, showcasing modern PHP development practices.

## About This Project

This system is a backend designed to accommodate book lending operations. The system supports three distinct user access scenarios:

### User Access Levels

* **Admin** - Full system access including book management, user administration, and complete system control
* **Member User** - Registered users who can access lending and return operations for books
* **Public** - Read-only access to view available books in the library

### Key Features

* **Intelligent Book Creation** - When adding books with ISBN, automatically fetches title and author data from Open Library API
* **Role-Based Access Control** - Three-tier permission system ensuring appropriate access levels
* **Concurrency Control** - Redis atomic locking prevents race conditions during concurrent operations
* **Interactive API Documentation** - Swagger OpenAPI provides comprehensive documentation with direct endpoint testing from the UI
* **Developer-Friendly Setup** - Single command initialization for easy project setup and consistent development environment across teams
* **Comprehensive Testing** - Complete feature test suite covering all user scenarios and workflows

### Architecture & Design Pattern

**MVC with Repository Pattern** implementation featuring:

* **Repository Pattern** - Separate Creator, Getter, and Updater classes for organized business logic
* **Thin Controllers** - HTTP concerns only, business logic in repositories
* **Atomic Operations** - Redis locking ensures data integrity during concurrent operations
* **Comprehensive Validation** - Custom rules prevent duplicate lending and ensure data consistency



## Author

**Hermawan Safrin**

* Email: hermawansafrin19@gmail.com
* GitHub: @hermawansafrin
* LinkedIn: Hermawan Safrin

## Prerequisites

Before you begin, ensure you have the following installed on your local machine:

* **PHP 8.4 or higher**
* **MySQL 8.0 or higher**
* **Redis Server** (for atomic locking and caching)
* **Composer** (PHP package manager)

## Installation

1. **Clone the repository:**

```bash
git clone https://github.com/hermawansafrin/book-lending.git
cd book-lending
```

2. **Install PHP dependencies:**

```bash
composer install
```

3. **Configure your environment:**
   * Copy `.env.example` to `.env`
   * Update the following in your `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   
   # Redis Configuration (Required for atomic locking)
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   
   # Cache Driver (Use Redis for production)
   CACHE_DRIVER=redis
   ```

4. **Generate application key:**

```bash
php artisan key:generate
```

5. **Initialize database with sample data:**

```bash
php artisan app:fresh-install
```

This command provides easy setup for initial project data, ensuring consistent development environment across team members.

## Technology Stack

* **Laravel 12 + PHP 8.4** - Latest framework with enhanced performance and modern language features
* **Redis** - Atomic locking for concurrency control and caching
* **Laravel Sanctum** - Token-based API authentication with role-based authorization
* **Swagger OpenAPI** - Interactive documentation with endpoint testing
* **MySQL** - Primary database with optimized queries

## Running the Application

You have multiple options to run the application:

1. **Using Laravel's built-in server:**

```bash
php artisan serve
```

2. **Using your local domain** (if configured):
   * Access the application through your configured local domain

## API Documentation

**Swagger OpenAPI Integration** provides comprehensive API documentation with interactive testing capabilities:

**Access:** `http://your-domain/api/documentation`

**Features:**
* **Complete API Reference** - Detailed documentation for all endpoints with descriptions and parameters
* **Direct Endpoint Testing** - Test API calls directly from the UI without external tools
* **Authentication Integration** - Login and test protected endpoints seamlessly
* **Request/Response Examples** - Live examples with sample data for easy understanding
* **Role-Based Testing** - Test different user access levels (Admin, Member, Public) from the interface

This interactive documentation eliminates the need for separate API testing tools and provides developers with immediate access to test functionality.

## Testing & Test Coverage

Run the comprehensive test suite:

```bash
php artisan test --testsuite=Feature
```

The project includes comprehensive **Feature Testing** that covers all system workflows:

#### Authentication Testing (`tests/Feature/Authentication/`)
- **User Registration** - Complete user registration flow with validation testing
- **User Login** - Authentication process with token generation and validation
- **Registration Validation** - Comprehensive input validation for user registration
- **Login Validation** - Authentication request validation and error handling

#### Book Management Testing (`tests/Feature/Book/`)
- **Book Creation** - Adding new books to the system (Admin functionality)
- **Book Creation Validation** - Input validation for book creation requests
- **Book Listing** - Retrieving books with pagination, search, and sorting
- **Book List Validation** - Query parameter validation for book listing
- **Book Lending** - Complete book lending workflow with availability checks
- **Book Lending Validation** - Validation for lending requests and business rules
- **Book Return** - Book return process with availability updates
- **Book Return Validation** - Return request validation and state checking

#### User Management Testing (`tests/Feature/User/`)
- **User Listing** - Admin functionality to view all users with pagination
- **User List Validation** - Query parameter validation for user listing
- **Admin Promotion** - Promoting users to admin status
- **Admin Promotion Validation** - Validation for admin promotion requests

## Testing Accounts

The following accounts are available for testing purposes:

1. **Administrator Account:**
   * Email: `admin@mail.test`
   * Password: `123456`
   * Permissions: Full system access, book management, user administration

2. **Regular User Account:**
   * Email: `user@mail.test`
   * Password: `123456`
   * Permissions: Book lending and returning operations

## API Endpoints Overview

### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User authentication

### Books
- `GET /api/books` - List books with pagination and search
- `POST /api/books` - Create new book (Admin only)
- `POST /api/books/{id}/lend` - Lend a book
- `POST /api/books/{id}/return` - Return a book

### Users
- `GET /api/users` - List users (Admin only)
- `POST /api/users/{id}/make-admin` - Promote user to admin (Admin only)

## License

This project is open-sourced software licensed under the MIT license.
