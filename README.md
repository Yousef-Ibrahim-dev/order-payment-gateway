# Laravel Payment Gateway Integration

This project integrates multiple payment gateways into a Laravel application. It demonstrates how to handle payments via PayPal and Credit Card, and how to extend the system with new gateways.

## Prerequisites

Before you begin, ensure that you have the following installed:

- [Docker](https://www.docker.com/)
- [Laravel Sail](https://laravel.com/docs/8.x/sail)
- [Composer](https://getcomposer.org/)

## Setup

1. Clone the repository to your local machine:

    ```bash
    git clone git@github.com:Yousef-Ibrahim-dev/order-payment-gateway.git
    cd order-payment-gateway
    ```

2. Copy the `.env.example` file to `.env`:

    ```bash
    cp .env.example .env
    ```
   
3. Install the dependencies using Composer:

    ```bash
    composer install
    ```
   
4. Generate the application key:

    ```bash
    ./vendor/bin/sail artisan key:generate
    ```
   
5. JWT SECRET

    ```bash
    ./vendor/bin/sail jwt:secret
    ```
   
6. Create a new database in your MySQL server and update the `.env` file with your database credentials.
7. Run the following command to create the database tables:

    ```bash
    ./vendor/bin/sail artisan migrate
    ```
   
8. Seed the database with sample data:

    ```bash
    ./vendor/bin/sail artisan db:seed
    ```
9. Start the Laravel Sail environment:

    ```bash
    ./vendor/bin/sail up
    ```
10. Access the application at `http://localhost`.

3. Update `.env` with your configuration values for your database, mail service, and PayPal credentials.

4. Build the Docker containers:

    ```bash
    ./vendor/bin/sail up -d
    ```


## Adding a New Payment Gateway

1. **Create a new gateway class** that implements the `PaymentGatewayInterface`. For example, a new class for Stripe can be created in `app/Services/Gateways/StripeGateway.php`.

2. **Update the `PaymentService` class** to include the new gateway in the `$gateways` array.

    ```php
    protected array $gateways = [
        'credit_card' => \App\Services\Gateways\CreditCardGateway::class,
        'paypal'      => \App\Services\Gateways\PayPalGateway::class,
        'stripe'      => \App\Services\Gateways\StripeGateway::class,  // New Gateway
    ];
    ```

3. **Create a new API endpoint** in the `PaymentController` if necessary, following the same pattern for creating payments.


## Postman Collection

You can import the Postman collection to test the API. Below are the endpoints available:

- **Create Payment:** POST `/api/orders/{id}/payments`
- **Get All Payments:** GET `/api/payments`
- **Get Payments for a Specific Order:** GET `/api/orders/{id}/payments`

path: `payment-gateway-collection.json`
