# PureDrop Dairy API

A Laravel-based backend project for PureDrop Dairy, a dairy delivery service. This project establishes the core database structure for products, customers, orders, and deliveries.

## Features

- Product management with price, unit, stock, and availability.
- Customer records with contact information and address.
- Orders linked to customers and products.
- Deliveries linked to orders, with scheduled dates, status, and proof-of-delivery path.
- Database factories and seeders for sample data.

## Requirements

- Docker Desktop
- Docker Compose

## Local Setup

1. Clone the repository:

   ```bash
   git clone https://github.com/ahmedwajid017-pixel/PureDrop-Dairy.git
   cd PureDrop-Dairy
   ```

2. Create your environment file:

   ```bash
   cp .env.example .env
   ```

3. Install PHP dependencies:

   ```bash
   docker compose run --rm laravel.test composer install
   ```

4. Start the application containers:

   ```bash
   docker compose up -d
   ```

5. Generate the application key:

   ```bash
   docker compose exec laravel.test php artisan key:generate
   ```

6. Run migrations and seed sample data:

   ```bash
   docker compose exec laravel.test php artisan migrate --seed
   ```

Make sure the database settings in `.env` match the MySQL service configured in `compose.yaml`.
## Sample Data

The database seeder creates at least 10 products and 5 customers using Laravel factories and Faker.

## Database Structure

- **Users:** Customer information.
- **Products:** Dairy product details, pricing, stock, and availability.
- **Orders:** Customer orders linked to products.
- **Deliveries:** Delivery schedule, status, and optional proof-of-delivery file path.

## Environment and Security

- `.env.example` provides a template for environment configuration.
- Do not commit `.env` or production credentials to version control.

## Project Status

Task 01: Database migrations, model relationships, factories, and sample data seeding completed.
