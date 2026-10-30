# PureDrop Dairy API

PureDrop Dairy is a Laravel-based REST API for managing dairy products, customer authentication, and orders.

## API Authentication

The PureDrop Dairy API uses Laravel Sanctum for token-based authentication.

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
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@example.com",
    "password": "Password123!"
  }'
```

The login response includes an API token. Use it in the `Authorization` header for protected endpoints.

### Get Authenticated User

```bash
curl http://localhost/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### Admin Dashboard

```bash
curl -X GET http://localhost/api/admin/dashboard \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_API_TOKEN"
```

Replace `YOUR_ADMIN_API_TOKEN` with a valid API token belonging to a user whose role is `admin`.

Only authenticated users with the `admin` role can access the admin dashboard. Customer accounts receive a `403 Forbidden` response.

---

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

---

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

The total price is calculated automatically using the product price and requested quantity.

The system also checks product availability and stock before placing an order and decreases the available stock after a successful order.

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

---

## Admin Email Notifications

PureDrop Dairy sends an email notification to the administrator whenever a customer successfully places an order.

The notification includes:

- Order ID
- Customer name and email
- Product name
- Quantity
- Total price
- Order status

Laravel queued mail is used to process order notification emails.

Mailtrap is used for development email testing.

### Run the Queue Worker

```bash
docker compose exec laravel.test php artisan queue:work --timeout=120
```

Check the configured Mailtrap inbox to verify that order notification emails are received.

---

## Running Tests

The project includes unit and feature tests for authentication, product management, and order management.

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
docker compose exec laravel.test php artisan test tests/Feature/Api/ProductAndOrderTest.php
```

### Test Coverage

The tests verify:

- Customer registration and login
- API token authentication
- Admin authorization
- Product listing and pagination
- Viewing individual products
- Successful order creation
- Order validation
- Product stock updates

---

## Technologies Used

- PHP
- Laravel
- Laravel Sanctum
- MySQL
- REST API
- Docker
- Mailtrap
- PHPUnit
- Git and GitHub
