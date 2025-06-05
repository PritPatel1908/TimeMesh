# Hostel Management System

A Laravel-based system for managing hostel residents, tracking their in/out status, and sending WhatsApp notifications.

## Features

-   User authentication
-   Dashboard with real-time updates
-   Track residents' in/out status
-   WhatsApp notification system
-   Daily and monthly reports
-   User management

## Requirements

-   PHP 8.1+
-   MySQL 5.7+
-   Composer
-   Node.js and NPM (for frontend assets)

## Installation

1. Clone the repository:

```
git clone <repository-url>
cd hostel
```

2. Install PHP dependencies:

```
composer install
```

3. Create a copy of the `.env` file:

```
cp .env.example .env
```

4. Generate application key:

```
php artisan key:generate
```

5. Configure your database in the `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hostel
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations:

```
php artisan migrate
```

7. Create a default admin user:

```
php artisan db:seed --class=AdminUserSeeder
```

8. Start the development server:

```
php artisan serve
```

9. Access the application at `http://localhost:8000`

## Default Login Credentials

-   User Code: `admin`
-   Password: `password`

## Database Structure

The system uses three main tables:

1. **users** - Stores resident information
2. **punch_logs** - Records entry/exit logs
3. **whatsapp_statuses** - Tracks WhatsApp message counts

## WhatsApp Integration

To integrate with WhatsApp, you need to:

1. Set up a WhatsApp Business API account
2. Configure the API credentials in the `.env` file
3. Implement the actual API calls in the `PunchLogController@processWhatsAppMessage` method

## License

This project is licensed under the MIT License.
