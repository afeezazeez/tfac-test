# Simple E-commerce Shopping Cart

A full-stack e-commerce shopping cart application built with Laravel and Vue.js (Inertia.js). This project demonstrates a complete shopping cart system with product browsing, cart management, stock notifications, and daily sales reporting.

## Description

This application provides a simple yet complete e-commerce shopping experience where authenticated users can:
- Browse products with pagination
- Add products to their shopping cart
- Update item quantities in the cart
- Remove items from the cart
- Checkout (simplified flow that creates orders and clears the cart)

The system also includes automated notifications for low stock products and daily sales reports sent to administrators.

## Features

### Core Functionalities

1. **Product Management**
   - Browse products with pagination
   - View product details (name, price, stock status)
   - Real-time stock status indicators (In Stock, Low Stock, Out of Stock)

2. **Shopping Cart**
   - Add products to cart with quantity selection
   - Update item quantities
   - Remove items from cart
   - View cart total and item count
   - Cart persists in database (not session/localStorage)
   - One cart per authenticated user

3. **Checkout**
   - Simplified checkout flow with confirmation modal
   - Creates orders and order items in the database
   - Decrements product stock upon checkout
   - Clears cart after successful checkout

4. **Low Stock Notifications**
   - Automatic email notifications when product stock falls below threshold
   - Configurable threshold via environment variable
   - Uses Laravel Model Observer pattern for decoupled notification logic

5. **Daily Sales Reports**
   - Automated daily sales report sent via email
   - Scheduled to run every evening at 6:00 PM UTC
   - Includes product sales summary with totals and statistics
   - HTML formatted email with professional styling

6. **Authentication**
   - User registration and login
   - Protected routes for cart operations

## Tech Stack

- **Backend**: Laravel 12.x
- **Frontend**: Vue.js 3 with Inertia.js
- **Styling**: Tailwind CSS
- **Database**: MySQL
- **Testing**: Pest PHP
- **Mail**: Laravel Mail (with Mailtrap support for testing)

## Requirements

- **PHP** >= 8.2 (https://www.php.net/downloads)
- **Composer** (https://getcomposer.org/download/)
- **Node.js** >= 18.x and **npm** (https://nodejs.org/)
- **MySQL** (for database)

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd tfac-test
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Database Configuration

Configure your database connection in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

This will:
- Create all necessary database tables
- Seed 50 sample products with relatable names and varied stock levels

### 6. Configure Queue Connection

Set the queue connection to `sync` in `.env` (for testing without a queue worker):

```env
QUEUE_CONNECTION=sync
```

For development with hot-reload:

```bash
npm run dev
```

## Running the Application

### Development Mode

Start the Laravel development server:

```bash
php artisan serve
```

This will launch the application at `http://localhost:8000`. You can now access the app in your browser at this URL.

**Frontend Development (Optional)**:

If you want hot-reload for frontend changes, run this in a separate terminal:

```bash
npm run dev
```

This starts the Vite dev server for frontend asset compilation. Note that `npm run dev` alone does not start the Laravel server - you still need to run `php artisan serve` in another terminal to access the application at `http://localhost:8000`.

**Note**: Make sure `QUEUE_CONNECTION=sync` is set in your `.env` file. This ensures that background jobs (like low stock notifications) run synchronously without requiring a separate queue worker. Alternatively, you can configure it to use `database` or `redis` if you have a queue worker running.

## Configuration

### Email Configuration

To receive low stock notifications and daily sales reports, configure email settings in `.env`:

#### Using Mailtrap (Recommended for Testing)

1. Sign up at https://mailtrap.io
2. Create an inbox and get your SMTP credentials
3. Add to `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
ADMIN_EMAIL=admin@example.com
```

#### Using Log Driver (Alternative for Testing)

```env
MAIL_MAILER=log
ADMIN_EMAIL=admin@example.com
```

Emails will be written to `storage/logs/laravel.log` instead of being sent.

### Low Stock Threshold

Configure the low stock threshold in `.env`:

```env
LOW_STOCK_THRESHOLD=5
```

Default is 5. Products with stock at or below this threshold will trigger notifications when stock crosses from above to at/below the threshold.

**Note**: In an ideal production scenario, this threshold would be configurable through an admin panel, allowing administrators to set different thresholds for different products or categories.

### Daily Sales Report Schedule

The daily sales report is scheduled to run at 6:00 PM UTC daily. To test it immediately, you can run:

```bash
php artisan sales:report
```

Or temporarily modify the schedule in `bootstrap/app.php` (currently set to run every minute for testing).

## Testing

The project includes comprehensive test coverage using Pest PHP.

### Run All Tests

```bash
php artisan test
```

### Test Coverage

The test suite includes:
- **Product Model Tests**: Stock status methods (isInStock, isLowStock, isOutOfStock, hasEnoughStock)
- **Cart Service Tests**: All cart operations (add, update, remove, clear, get count)
- **Cart Controller Tests**: HTTP endpoints and validation
- **Order Service Tests**: Order creation and stock decrementing
- **Checkout Service Tests**: Checkout flow
- **Low Stock Notification Tests**: Observer and job tests
- **Daily Sales Report Tests**: Command and email tests
- **Product Controller Tests**: Product listing and pagination

**Total**: 106 tests with 296 assertions

## Project Structure

```
app/
├── Console/
│   └── Commands/
│       └── DailySalesReportCommand.php
├── Exceptions/
│   └── ClientErrorException.php
├── Http/
│   ├── Controllers/
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   └── ProductController.php
│   ├── Middleware/
│   │   └── HandleInertiaRequests.php
│   ├── Requests/
│   │   ├── CartStoreRequest.php
│   │   └── CartUpdateRequest.php
│   └── Resources/
│       ├── CartResource.php
│       ├── CartItemResource.php
│       └── ProductResource.php
├── Jobs/
│   └── LowStockNotificationJob.php
├── Mail/
│   ├── DailySalesReportMail.php
│   └── LowStockNotificationMail.php
├── Models/
│   ├── Cart.php
│   ├── CartItem.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── Product.php
├── Observers/
│   └── ProductObserver.php
└── Services/
    ├── CartService.php
    ├── CheckoutService.php
    ├── OrderService.php
    └── ProductService.php
```

## Decisions

### Architecture Pattern: Service Layer

**Decision**: We used a Service Layer approach for this project.

**Rationale**:
- Using a full Repository pattern would be overkill for this task
- Eloquent already provides a clean Active Record implementation
- The Service Layer keeps business logic organized without unnecessary abstraction
- Focus on delivery and maintainability

**Implementation**:
- **Controllers**: Thin, handle HTTP requests, validation, and delegate to services
- **Services**: Handle complex business logic (e.g., CartService for cart operations, stock checking)
- **Models**: Eloquent models with data-centric methods (isInStock(), isLowStock(), isOutOfStock())
- **Form Requests**: Validation logic separated from controllers
- **Jobs**: For async operations (low stock notifications)
- **Scheduled Commands**: For daily sales reports

### Cart Database Design: One Cart Per User

**Decision**: One cart per user (simplified approach).

**Rationale**:
- Meets the requirements for a "simple e-commerce shopping cart"
- Aligns with the "keep it simple" directive
- Demonstrates pragmatic decision-making (choosing simplicity over complexity when requirements don't demand it)
- Faster to implement and test
- Can be extended later if needed (e.g., saved carts, order history)

**Database Structure**:
- **carts table**: `id`, `user_id` (unique foreign key), `timestamps`
- **cart_items table**: `id`, `cart_id` (foreign key), `product_id` (foreign key), `quantity`, `timestamps`

**Design Notes**:
- Unique constraint on `user_id` in carts table ensures one active cart per user
- Proper foreign key constraints with cascade delete
- Normalized design (carts and cart_items are separate tables)

This design keeps the implementation clean and simple while maintaining proper database relationships and data integrity.

### Low Stock Notification: Model Observer Pattern

**Decision**: Use Laravel's Model Observer pattern for low stock notifications.

**Rationale**:
- **Decoupled**: Notification logic is separated from business logic (cart operations, admin updates, etc.)
- **Automatic**: Works for any stock update (checkout, admin panel, imports, etc.) without modifying multiple places
- **Maintainable**: Single place to manage low stock detection logic
- **Testable**: Easy to test observer behavior independently

**Implementation**:
- **ProductObserver** listens to the `updated` event on the Product model
- Checks if `stock_quantity` was changed
- Dispatches `LowStockNotificationJob` when stock crosses the threshold (from above threshold to at/below threshold)
- Uses attribute-based registration: `#[ObservedBy([ProductObserver::class])]` on the Product model

**Important Limitation**:
- Observers work with **Eloquent model methods** that trigger model events (`save()`, `update()`, `decrement()`, etc.)
- Observers **do NOT work** with **Query Builder** methods (`DB::table()->update()`, `Product::where()->update()`, etc.)
- To ensure observers fire, always use Eloquent model instances

### Checkout Simulation

**Decision**: Implement a simplified checkout flow that creates orders and clears the cart, without payment processing.

**Rationale**:
- Meets the "keep it simple" requirement
- Demonstrates order creation and stock management
- Can be extended later to include payment processing (Stripe, PayPal, etc.)

**Implementation**:
- When checkout is confirmed, an order is created with "completed" status
- Order items are created from cart items
- Product stock is decremented
- Cart is cleared
- Success message is displayed

**Future Enhancement**: This can be extended to implement a real checkout system with:
- Payment processing integration
- Order status workflow (pending → processing → completed)
- Order history for users
- Email confirmations

### Daily Sales Report: Immediate Order Completion

**Decision**: Orders are created with "completed" status immediately (instead of "pending" or "processing").

**Rationale**:
- Allows testing of the daily sales report functionality without payment integration
- Simplifies the implementation for the test project
- In production, orders would typically start as "pending" and transition to "completed" after payment confirmation

**Future Enhancement**: The report can be extended to:
- Filter by order status
- Include PDF or CSV attachments
- Support multiple date ranges
- Include more detailed analytics

