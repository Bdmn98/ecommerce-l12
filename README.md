E-Commerce API (Laravel)

Hello 👋
This is a coding challenge project to simulate a simple e-commerce backend built with Laravel.
It covers authentication, product/category management, cart, orders, and mock payments.

🚀 Setup (MySQL)
git clone https://github.com/Bdmn98/ecommerce-l12.git
cd ecommerce-l12
composer install
cp .env.example .env
php artisan key:generate

1) Configure .env for MySQL

Update these lines:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_l12
DB_USERNAME=root
DB_PASSWORD=your_mysql_password


Create the database if it doesn’t exist:

mysql -u root -p -e "CREATE DATABASE ecommerce_l12 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"


(Optional but recommended):

APP_URL=http://localhost:8000
SANCTUM_STATEFUL_DOMAINS=localhost
SESSION_DOMAIN=localhost

2) Migrate & seed
php artisan migrate --seed


Seeders will create:

2 admins

admin1@example.com / password

admin2@example.com / password

10 customers (customers1…10@example.com
, password: password)

5 categories, 20 products, 10 carts, 15 orders

3) Run the API
php artisan serve
# -> http://127.0.0.1:8000

🔐 Auth (Sanctum)

POST /api/auth/register

POST /api/auth/login

POST /api/auth/logout (auth)

GET /api/auth/me (auth)

On login/register you’ll get a token.
Use it like:

Authorization: Bearer <token>

🗂️ Main Endpoints

Public

GET /api/categories

GET /api/products

GET /api/products/{id}

Admin (auth + role:admin)

POST /api/categories

PUT /api/categories/{id}

DELETE /api/categories/{id}

POST /api/products

PUT /api/products/{id}

DELETE /api/products/{id}

PUT /api/orders/{order}/status

Customer (auth + role:customer)

GET /api/cart

POST /api/cart

PUT /api/cart/{cart}

DELETE /api/cart/{cart}

POST /api/orders (creates from cart; stock-guard middleware)

GET /api/orders

POST /api/orders/{order}/payments

GET /api/payments/{payment}

🧪 Tests & Coverage
php artisan test
# or, with coverage (requires Xdebug/PCOV installed)
php artisan test --coverage


Target: 85%+ across controllers & services.
Feature tests include register/login/product create/cart update/order placement.
Unit test exists for OrderService.

📬 Postman

Import the collection:

docs/postman/ecommerce-l12.postman_collection.json


Environments:

{{baseUrl}} = http://127.0.0.1:8000

Bearer token will be set after calling login.

🧰 Dev Tips

Clear caches if you change config/routes:

php artisan optimize:clear


Re-seed quickly:

php artisan migrate:fresh --seed


Default test DB uses your configured MySQL (or set DB_* in phpunit.xml).

📄 License

For challenge/demo use.