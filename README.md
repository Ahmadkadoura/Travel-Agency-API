# Laravel Travel Agency API

A RESTful API system for a travel agency to manage travels and tours efficiently.

## Features

- **Admin Endpoints**:
  - Manage users, travels, and tours, including core operations like creation and updates.
- **Public Endpoints**:
  - Retrieve paginated lists of travels and tours.
  - Search and filter tours by price range, date range, and other criteria.
  - Sort tours by price and starting date.
- **Pagination**:
  - Efficiently handles large datasets to ensure scalability.
- **Role-Based Access Control**:
  - Implements middleware for admin and editor roles to secure endpoints.
- **Clean Architecture**:
  - Built with reusable components using Laravel’s Service Providers and Repository Patterns.
- **Database Optimization**:
  - Relational database structure with well-defined relationships between travels, tours, and users.

## Installation

### Prerequisites

Ensure you have the following installed:
- PHP >= 7.4
- Composer
- Laravel >= 8.x
- A web server (e.g., Apache or Nginx)
- MySQL or another supported database

Please check the official Laravel installation guide for server requirements before you start. Official Documentation

Alternative installation is possible without local dependencies relying on Docker.

Clone the repository 

   git clone https://github.com/your-username/laravel-travel-agency-api.git


Switch to the repo folder

    cd laravel-travel-agency-api

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate


Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**TL;DR command list**

   git clone https://github.com/your-username/laravel-travel-agency-api.git
   cd laravel-travel-agency-api
   composer install
   cp .env.example .env
   php artisan key:generate
    
**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate
    php artisan serve

## Database seeding

**Populate the database with seed data with relationships which includes users, articles, comments, tags, favorites and follows. This can help you to quickly start testing the api or couple a frontend and start using it with ready content.**

Run the database seeder and you're done

    php artisan db:seed
    
