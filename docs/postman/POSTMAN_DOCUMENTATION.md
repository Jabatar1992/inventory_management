# Inventory Management API - Postman Documentation

This project now includes a ready Postman collection:

- `docs/postman/inventory_management.postman_collection.json`

## 1) Import into Postman

1. Open Postman.
2. Click **Import**.
3. Select `docs/postman/inventory_management.postman_collection.json`.
4. Ensure collection variable `base_url` is set to:
   - `http://localhost/inventory_management/api`

## 2) Auth Flow

1. Run `User -> Register User` (optional if user already exists).
2. Run `User -> Login` with a valid `user_id` and `password`.
3. The test script stores `access_token` automatically in your Postman environment.
4. Run authenticated endpoints:
   - `Stock Management -> Add Stock (Auth)`
   - `Stock Management -> Reduce Stock When Sell (Auth)`

## 3) Endpoint Groups Included

- User
  - `POST /user/reg_user.php`
  - `POST /user/login.php`
- Product
  - `POST /product/add_product.php`
  - `GET /product/list_product.php`
  - `POST /product/update_product.php`
  - `POST /product/delete_product.php`
- Stock Management
  - `POST /stock_management/add_stock.php` (Bearer token required)
  - `POST /stock_management/reduce_stock_whensell.php` (Bearer token required)
  - `GET /stock_management/stock_history.php` (optional query params: `product_id`, `user_id`)

## 4) Publish Docs in Postman

1. Open the imported collection.
2. Click the collection menu (`...`) -> **View documentation**.
3. Add examples by sending requests and saving responses.
4. Click **Publish Docs** to get a shareable URL.

## 5) Notes based on current code

- Most `POST` endpoints expect `application/x-www-form-urlencoded` body.
- API response shape is standardized as:
  - `status` (bool)
  - `text` (message)
  - `data` (payload)
  - `time`, `method`, `endpoint`, `error`
- In `api/user/login.php`, login checks table `user` with plain password comparison.
  - If registration was used, it stores hashed passwords, so login may fail unless login logic is updated.