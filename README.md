# INOVANT E-commerce Platform

## Requirements
- PHP 8.4+
- MySQL 8.0+
- Laravel 12.x
- Composer

## Installation
1. Clone repository
2. Run `composer install`
3. Copy `.env.example` to `.env`
4. Configure database settings
5. Run `php artisan migrate`
6. Import sample data from `database/inovant_ecommerce_final.sql`
7. Start server: `php -S localhost:8000 -t public/`

## Admin Panel
Access: http://localhost:8000/admin
Features: Product management, Order tracking, Cart management


## API Testing with Postman

### Import Collection
1. Open Postman
2. Click "Import" 
3. Select the file: `postman/INOVANT_E-commerce_API.postman_collection.json`
4. The collection will be imported with all endpoints

### Environment Setup
The collection uses the variable `{{base_url}}` which is set to `http://localhost:8000`

### Testing Order
1. Start Laravel server: `php artisan serve`
2. Test endpoints in this order:
   - Products → Get All Products
   - Products → Create Product
   - Cart → Add to Cart
   - Orders → Checkout

