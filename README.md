## API Authentication

The PureDrop Dairy API uses Laravel Sanctum for token-based authentication.

### API Endpoints

| Method | Endpoint | Description | Authentication |
|---|---|---|---|
| POST | `/api/register` | Register a customer | Not required |
| POST | `/api/login` | Log in and receive an API token | Not required |
| GET | `/api/user` | Get the authenticated user's details | Bearer token |
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


### Run Authentication Tests

```bash
docker compose exec laravel.test php artisan test --filter=AuthenticationTest
```
