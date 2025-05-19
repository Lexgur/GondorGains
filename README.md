# GondorGains

GondorGains is a gym workout challenge application that motivates users to complete workout exercises through engaging challenges and quests.

## Project Overview

GondorGains is a PHP web application that allows users to:
- Register and login to track their progress
- View and complete workout challenges
- Track personal fitness quests
- Access a personalized dashboard

## Requirements

- PHP 8.4+
- Composer
- PDO Extension
- Web server (Apache/Nginx)
- SQLite (default) or other PDO-compatible database

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/Lexgur/GondorGains GondorGains
cd GondorGains

# 2. Install dependencies
composer install

# 3. Configure your environment
cp environment.config.example.php environment.config.php

# 4. Edit the environment.config.php file with your database configuration
# - Default configuration uses SQLite
# - Modify 'dsn' for a different database (MySQL, PostgreSQL, etc.)

# 5. Set up the database
php bin/script CreateDatabaseScript

# 6. (Optional) Seed the database with tables
php bin/script RunMigrationScript

# 7. (Optional) Seed the database with initial data
php bin/script RunSeedersScript
```

## Running the Application

### Development Server

```bash
php -S localhost:8000 -t public
```

Then access the application at: http://localhost:8000

## Project Structure

- /bin - Command-line scripts
- /public - Web accessible files
- /src - Application source code
  - /Attribute - Custom PHP attributes
  - /Controller - Web controllers
  - /Exception - Custom exceptions
  - /Migration - Database migrations
  - /Model - Data models
  - /Repository - Data access layer
  - /Script - CLI scripts
  - /Seeder - Database seeders
  - /Service - Business logic
  - /Validation - Input validators
- /templates - Twig templates
- /tests - PHPUnit tests
- /tmp - Temporary files and data storage

## Command-line Scripts

The application includes command-line scripts for various tasks:

```bash
php bin/script [ScriptName]
```

Available scripts include:
- CreateDatabaseScript - Set up the database schema
- RunMigrationsScript - Runs the migrations, adds the tables to the project
- RunSeedersScript - Seeds the tables with needed initial data.

## Testing

Run the PHPUnit tests with a code coverage report:

```bash
composer run phpunit
```

## Development Tools & Commands

The project includes several Composer scripts for code quality:

### PHP Mess Detector
```bash
composer run phpmd
```

### PHPStan Static Analysis
```bash
composer run phpstan
```

### PHP CS Fixer
```bash
composer run php-cs-fixer-set
composer run php-cs-fixer-run
```

### Run All Code Quality Tools
```bash
composer run code-style
```

## Features

- User registration and authentication
- Workout challenge creation and tracking
- Personal quest management
- User dashboard with progress tracking

## Configuration Files

- config.php - Main configuration
- dev.config.php - Development environment configuration
- test.config.php - Testing environment configuration
- environment.config. example.php - Example config (copy to environment.config.php)

## Author

Lexgur (edgaras.malukas@gmail.com)

## Dependencies

Main:
- PSR Container (psr/container)
- PDO Extension
- Twig Template Engine

Development:
- PHP Mess Detector
- PHPStan
- PHP CS Fixer
- PHPUnit
