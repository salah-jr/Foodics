# Foodics Task

## Overview

This is a simple system for a restaurant that tracks stock levels of ingredients, handles orders, and sends email alerts to merchants when ingredient stocks fall below 50%.

## Features

- Order placement with stock deduction
- Email notification to the merchant when ingredient stock is low
- Ingredient and product management

## Technologies Used

- PHP 8.1
- Laravel 10
- PHPUnit for testing
- Laravel Mailable for email notifications

## Installation

Follow these steps to set up the project:

1. Clone the repository: `git clone https://github.com/salah-jr/Foodics-Task.git`
2. Navigate to the project directory
3. Install dependencies: `composer install`
4. Create a `.env` file based on `.env.example` and configure your database or use a sqlite database.
5. Generate an application key: `php artisan key:generate`
6. Migrate the database: `php artisan migrate`
7. Seed the sample data: `php artisan db:seed`
8. Start the development server: `php artisan serve`

## System Usage

- To place an order, send a POST request to `/api/place-order` with a payload structured as follows:
  
    ```
   {
        "products": [
            {
                "product_id": 1,
                "quantity": 1
            }
        ]
    }
   ```
  Kindly note that this endpoint has been intentionally left without authentication for the purpose of simplifying testing and aligning with the task description.

## Testing

Run the test suite using PHPUnit: `php artisan test`

## System Components

### Database Tables
- **orders**: Stores information about customer orders.
- **order_products**: Contains details of products included in each order.
- **products**: Represents various products available for ordering.
- **ingredients**: Manages the inventory and stock levels of ingredients.
- **product_ingredients**: Links products to their respective ingredients with quantities.

### Request Validation
- **Order Request Validation**: Request validation is handled by a custom request class located at `app/Http/Requests/OrderRequest.php`.

### Controller
- **Order Controller**: The order controller, located at `app/Http/Controllers/Api/OrderController.php`, manages the HTTP request handling and responses for placing orders.

### Service Logic
- **Order Service Logic**: The order service class, found at `app/Http/Services/OrderService.php`, contains the core business logic for processing orders.

### Tests
- **Order Controller Test Cases**: The test cases for the order controller are implemented in the test class located at `tests/Feature/OrderControllerTest.php`. These tests ensure that the order placement, validation and stock management functionality works as expected.

