# E-Commerce Order Management System (Laravel 12)

A high-performance, scalable REST API for managing e-commerce operations. Built with modern Laravel architecture (Actions, Repositories, Enums) and strict typing.

## 🚀 Core Features

*   **Inventory Management:** Real-time stock tracking with pessimistic locking to prevent race conditions.
*   **Product Search:** Integrated database search (Searchable by Name, Description, or SKU).
*   **Order Processing:** 
    *   Atomic Database Transactions.
    *   Automatic Invoice PDF Generation (Queued).
    *   Smart Inventory Rollback on Cancellation.
*   **Security:** JWT Authentication with Role-Based Access Control (RBAC).

## 🛠 Tech Stack

*   **Framework:** Laravel 12 (PHP 8.3)
*   **Database:** MySQL 8
*   **Auth:** PHP-Open-Source-Saver JWT Auth
*   **Queue:** Database Driver (Async Jobs)
*   **PDF:** DomPDF

## ⚙️ Installation Guide

### 1. Prerequisites
*   PHP 8.2 or higher (with `sodium` and `fileinfo` extensions enabled)
*   Composer
*   MySQL

### 2. Setup
```bash
# Clone Repository
git clone <your-repo-url>
cd project-name

# Install Dependencies
composer install

#Build assets
npm install
npm run build

# Environment Setup
cp .env.example .env
php artisan key:generate

# JWT Setup
php artisan jwt:secret
```
### 3. Database & Migrations
Configure your .env file with database credentials, then run:

```bash
php artisan migrate
```

### 4. Run the Application
You need two terminals open:
Terminal 1 (API Server):

```bash
php artisan queue:work
```
## 🔑 API Documentation

### Authentication
*   **Header:** `Authorization: Bearer <token>`

| Method | Endpoint | Description | Body |
| :--- | :--- | :--- | :--- |
| POST | `/api/v1/auth/login` | Login | `{email, password}` |
| POST | `/api/v1/auth/register` | Register | `{name, email, password, role}` |

### Products
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| GET | `/api/v1/products?q=nike` | Public | Search products |
| POST | `/api/v1/products` | Vendor | Create product `{name, variants: [...]}` |

### Orders
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| POST | `/api/v1/orders` | Customer | Place order `{items: [{variant_id, qty}]}` |
| PATCH | `/api/v1/orders/{id}/cancel` | Customer | Cancel & Restore Stock |
| GET | `/api/v1/my-orders` | Customer | View Order History |

## 🧪 Testing Strategy

This project uses PHPUnit for feature testing.

**Run Tests:**
```bash
php artisan test
```

**Key Test Case (`OrderFlowTest`):**
1.  Creates a User and Product.
2.  Places an order via API.
3.  Asserts Order is created in DB.
4.  Asserts Stock is deducted in DB.

## 👤 Author
*   **Name:** Firoz Ebna Jobaier
*   **Email:** firoz.jobaier@gmail.com

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Connect-blue?style=for-the-badge&logo=linkedin)](https://www.linkedin.com/in/firoz-ebna-jobaier)
[![Fork me on GitHub](https://img.shields.io/badge/Fork_on_GitHub-000?style=for-the-badge&logo=github)](https://github.com/yenHunter)
