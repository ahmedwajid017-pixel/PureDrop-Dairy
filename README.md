# PureDrop Dairy API

![Laravel](https://img.shields.io/badge/Laravel-13-red)
![PHP](https://img.shields.io/badge/PHP-8.4-blue)
![License](https://img.shields.io/badge/License-MIT-green)

[![PureDrop Dairy CI](https://github.com/ahmedwajid017-pixel/PureDrop-Dairy/actions/workflows/ci.yaml/badge.svg)](https://github.com/ahmedwajid017-pixel/PureDrop-Dairy/actions/workflows/ci.yaml)

PureDrop Dairy is a Laravel-based REST API designed to manage dairy products, customer authentication, orders, and delivery proof uploads.

The API provides secure token-based authentication, role-based authorization, product management, order processing, automatic stock updates, email notifications, and delivery proof image storage.

## Live Application and Documentation

- **Live API:** https://puredrop-dairy-production.up.railway.app
- **Interactive Swagger UI:** https://puredrop-dairy-production.up.railway.app/api/documentation
- **GitHub Repository:** https://github.com/ahmedwajid017-pixel/PureDrop-Dairy
- **GitHub Actions CI:** [View Workflow](https://github.com/ahmedwajid017-pixel/PureDrop-Dairy/actions)

## Features

- Customer registration and login using Laravel Sanctum.
- Secure API token authentication.
- Role-based authorization for admin routes.
- Dairy product listing and details with pagination.
- Order creation and customer order management.
- Automatic order total calculation.
- Product stock validation and automatic stock updates.
- Admin email notifications when orders are placed.
- Delivery proof image uploads with ownership validation.
- Persistent storage for uploaded delivery proof images.
- Product listing cache for five minutes.
- API rate limiting (60 requests per minute).
- Automated tests using PHPUnit.
- Code formatting checks using Laravel Pint.
- Continuous Integration using GitHub Actions.
- Interactive API documentation using L5-Swagger and OpenAPI.
- Bearer Token authentication in Swagger UI.

## Technology Stack

- PHP 8.4
- Laravel 13
- Laravel Sanctum
- MySQL
- REST API
- Docker and Docker Compose
- Laravel Queue
- Mailtrap
- PHPUnit
- Laravel Pint
- Git and GitHub
- GitHub Actions
- L5-Swagger
- OpenAPI / Swagger UI
- Railway

## Installation and Setup

### Requirements

Make sure you have installed:

- Docker Desktop
- Git
- Composer (optional when using the Docker container)

### 1. Clone the Repository

```bash
git clone https://github.com/ahmedwajid017-pixel/PureDrop-Dairy.git
cd PureDrop-Dairy
```

### 2. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Configure your database settings in `.env` to match the MySQL service in `compose.yaml`.

Configure the mail settings if you want to test email notifications.

### 3. Start Docker Containers

```bash
docker compose up -d
```

### 4. Install Dependencies

```bash
docker compose exec laravel.test composer install
```

### 5. Generate Application Key

```bash
docker compose exec laravel.test php artisan key:generate
```

### 6. Run Database Migrations and Seeders

```bash
docker compose exec laravel.test php artisan migrate --seed
```

This command creates the required database tables and seeds the initial development data.

### 7. Generate Public Storage Link

```bash
docker compose exec laravel.test php artisan storage:link
```

The API is now ready to run in your local development environment.

## API Authentication

PureDrop Dairy uses Laravel Sanctum for secure token-based API authentication.

### Authentication Endpoints

| Method | Endpoint | Description | Authentication |
|---|---|---|---|
| POST | `/api/register` | Register a customer | Not required |
| POST | `/api/login` | Log in and receive an API token | Not required |
| GET | `/api/user` | Get authenticated user details | Bearer token |
| GET | `/api/admin/dashboard` | Access the admin dashboard | Admin token required |

### Register a Customer

```bash
curl -X POST http://localhost/api/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Customer",
    "email": "customer@example.com",
    "password": "Password123!",
    "password_confirmation": "Password123!"
  }'
```

### Login

```bash
curl -X POST http://localhost/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@example.com",
    "password": "Password123!"
  }'
```

The login response includes an API token. Use this token in the `Authorization` header when accessing protected endpoints.

### Get Authenticated User

```bash
curl http://localhost/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### Admin Dashboard

```bash
curl http://localhost/api/admin/dashboard \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_API_TOKEN"
```

Replace `YOUR_ADMIN_API_TOKEN` with a valid token belonging to a user whose role is `admin`.

Only authenticated users with the `admin` role can access the admin dashboard. Customer accounts receive a `403 Forbidden` response.

## Product Management API

The Product API allows customers to view available dairy products.

### Product Endpoints

| Method | Endpoint | Description | Authentication |
|---|---|---|---|
| GET | `/api/products` | List available products with pagination | Not required |
| GET | `/api/products/{id}` | View a single available product | Not required |

### List Products

```bash
curl -X GET "http://localhost/api/products?page=1" \
  -H "Accept: application/json"
```

Products are returned in paginated JSON format, with up to 10 products per page.

### View a Single Product

```bash
curl -X GET http://localhost/api/products/1 \
  -H "Accept: application/json"
```

The response includes product information such as:

- Product ID
- Name
- Description
- Price
- Unit
- Available stock
- Availability status

Unavailable products cannot be viewed through the product detail endpoint.

### Product Caching

The product listing endpoint uses Laravel's caching system to improve API performance.

- Product listing responses are cached for **5 minutes (300 seconds)**.
- Cached results are reused for subsequent requests during the cache period.
- Pagination pages use separate cache keys.

## Order Management API

Authenticated customers can place orders and view their own orders.

### Order Endpoints

| Method | Endpoint | Description | Authentication |
|---|---|---|---|
| GET | `/api/orders` | List the authenticated customer's orders | Bearer token |
| POST | `/api/orders` | Place a new order | Bearer token |
| GET | `/api/orders/{id}` | View a specific order | Bearer token |

### Place an Order

```bash
curl -X POST http://localhost/api/orders \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

The API validates the product ID and quantity before creating an order.

The system:

- Checks product availability and stock.
- Calculates the total price automatically.
- Creates the order.
- Decreases the available product stock after a successful order.
- Creates a delivery record for the order.
- Sends an email notification to the administrator.

The order response includes:

- Order ID
- Quantity
- Total price
- Order status
- Product details
- Customer details

### List Customer Orders

```bash
curl -X GET http://localhost/api/orders \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### View a Specific Order

```bash
curl -X GET http://localhost/api/orders/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

Customers can only access their own orders.

## Delivery Proof Upload API

Authenticated customers can upload delivery proof images for deliveries belonging to their own orders.

### Delivery Endpoint

| Method | Endpoint | Description | Authentication |
|---|---|---|---|
| POST | `/api/deliveries/{delivery}/proof` | Upload delivery proof image | Bearer token |

### Upload Delivery Proof

```bash
curl -X POST http://localhost/api/deliveries/1/proof \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -F "proof=@delivery-proof.png"
```

Replace `YOUR_API_TOKEN` with a valid customer API token and use the appropriate delivery ID and image file.

### Image Validation

The delivery proof upload endpoint validates:

- Image is required.
- Allowed formats: JPEG, JPG, PNG, and WebP.
- Maximum file size: 5 MB.

### File Storage

- Uploaded images are stored on Laravel's public disk.
- Files are saved inside `storage/app/public/proofs`.
- The public storage link makes uploaded images accessible through `/storage`.
- The delivery record stores the uploaded image path in `proof_path`.
- The API returns a public `proof_url` after a successful upload.
- Customers can only upload proof for deliveries belonging to their own orders.

### Persistent Storage on Railway

Railway persistent storage is configured for delivery proof images.

The persistent volume is mounted at:

```text
/app/storage/app/public
```

This ensures uploaded proof images remain available across application restarts and redeployments.

### Example Successful Response

```json
{
  "message": "Delivery proof uploaded successfully.",
  "proof_url": "https://your-domain/storage/proofs/example.png"
}
```

## API Rate Limiting

The API uses Laravel's throttle middleware to protect endpoints from excessive requests.

- Maximum requests: **60 requests per minute**.
- The limit applies to routes in the API middleware group.
- Requests exceeding the limit receive an HTTP `429 Too Many Requests` response.

## Admin Email Notifications

PureDrop Dairy sends an email notification to the administrator whenever a customer successfully places an order.

The notification includes:

- Order ID
- Customer name and email
- Product name
- Quantity
- Total price
- Order status

Laravel queued mail is used to process order notification emails. Mailtrap is used for development email testing.

### Run the Queue Worker

```bash
docker compose exec laravel.test php artisan queue:work --timeout=120
```

Check the configured Mailtrap inbox to verify that order notification emails are received.

## API Documentation

The project uses L5-Swagger and OpenAPI to provide interactive API documentation.

### Interactive Swagger UI

**Live Documentation:**

https://puredrop-dairy-production.up.railway.app/api/documentation

**Local Documentation:**

http://localhost/api/documentation

Swagger UI allows developers to:

- Explore available API endpoints.
- View request parameters and response descriptions.
- Test API requests directly from the browser using **Try it out**.
- Authenticate using Laravel Sanctum Bearer Tokens.
- Understand authentication and authorization requirements.

The documentation covers:

- Authentication
- Admin authorization
- Products
- Orders
- Deliveries

### Using Bearer Token Authentication in Swagger

1. Open the Swagger UI URL.
2. Find the **Authorize** option.
3. Enter a valid API token in the Bearer Token field.
4. Click **Authorize**.
5. Use **Try it out** to test protected endpoints.

The `/docs` route serves the raw OpenAPI JSON documentation, while `/api/documentation` displays the interactive Swagger UI.

## Continuous Integration (GitHub Actions)

GitHub Actions is configured to automatically run the project's quality checks when code is pushed to the `main` branch or a pull request targets `main`.

The CI workflow performs:

1. Checks out the repository.
2. Sets up PHP 8.4.
3. Installs Composer dependencies.
4. Prepares the Laravel testing environment.
5. Runs Laravel Pint to check code formatting.
6. Runs automated PHPUnit tests using SQLite.

The CI badge at the top of this README displays the workflow status.

**Workflow:** [PureDrop Dairy CI](https://github.com/ahmedwajid017-pixel/PureDrop-Dairy/actions/workflows/ci.yaml)

## Running Tests

The project includes automated unit and feature tests for authentication, product management, order management, delivery proof uploads, caching, and rate limiting.

### Run All Tests

```bash
docker compose exec laravel.test php artisan test
```

### Run Authentication Tests

```bash
docker compose exec laravel.test php artisan test --filter=AuthenticationTest
```

### Run Product and Order Tests

```bash
docker compose exec laravel.test php artisan test --filter=ProductAndOrderTest
```

### Run Delivery Proof Tests

```bash
docker compose exec laravel.test php artisan test --filter=DeliveryProofTest
```

### Run Rate Limiting Tests

```bash
docker compose exec laravel.test php artisan test --filter=RateLimitingTest
```

### Check Code Formatting

```bash
docker compose exec laravel.test ./vendor/bin/pint --test
```

### Test Coverage

The automated tests verify:

- Customer registration and login.
- API token authentication.
- Admin authorization.
- Product listing and pagination.
- Viewing individual products.
- Successful order creation.
- Order validation.
- Product stock updates.
- Successful delivery proof image uploads.
- Delivery proof file storage.
- Product caching.
- API rate limiting and HTTP 429 responses.

### Latest Test Results

- **14 tests passed.**
- **45 assertions passed.**
- **Laravel Pint passed for all 56 files.**
- **GitHub Actions CI workflow completed successfully.**

These results reflect the latest verified local test and formatting run.

## Deployment

**Hosting Platform:** Railway

**Live Application URL:**

https://puredrop-dairy-production.up.railway.app

**Interactive Swagger UI:**

https://puredrop-dairy-production.up.railway.app/api/documentation

### Production Configuration

The application is deployed on Railway with:

- Laravel application service.
- Railway MySQL database.
- Production environment configuration.
- Persistent storage for delivery proof images.
- Public access to uploaded proof images.
- GitHub Actions CI for automated code quality and testing checks.

## Repository

GitHub: https://github.com/ahmedwajid017-pixel/PureDrop-Dairy

## License

This project is open-sourced under the MIT License.