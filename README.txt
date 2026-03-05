# Vehicle Rental System

A comprehensive PHP-based vehicle rental management system with role-based access control for Admins, Staff, and Customers.

## Features

### Admin Features
- Dashboard with key metrics
- Manage Vehicles (add, edit, delete)
- Manage Bookings
- Manage Customers
- Manage Payments
- Manage Staff
- Generate Reports
- View Revenue Analytics

### Staff Features
- Create Bookings
- Confirm Bookings
- Process Vehicle Returns
- Record Payments
- View Pending Bookings

### Customer Features
- Browse Available Vehicles
- Book Vehicles
- View My Bookings
- Make Payments
- Manage Profile
- Change Password

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx Web Server
- Bootstrap 5.3
- jQuery (optional)

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/vehicle-rental-system.git
   cd vehicle_rental_system
   ```

2. Create a MySQL database:
   ```
   mysql -u root -p < database/vehicle_rental.sql
   ```

3. Update database configuration in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'vehicle_rental');
   ```

4. Upload files to your web server or local Apache directory

5. Access the application:
   ```
   http://localhost/vehicle_rental_system
   ```

## Default Credentials

### Admin
- Email: admin@vehiclerental.com
- Password: password (hashed: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)

### Staff
- Email: staff1@vehiclerental.com
- Password: password

### Customer
- Email: john@example.com
- Password: password

## File Structure

```
vehicle_rental_system/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── auth_check.php        # Authentication middleware
│   ├── header.php            # HTML header template
│   ├── navbar.php            # Navigation bar template
│   └── footer.php            # HTML footer template
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── manage_vehicles.php   # Vehicle management
│   ├── manage_bookings.php   # Booking management
│   ├── manage_customers.php  # Customer management
│   ├── manage_payments.php   # Payment management
│   ├── manage_staff.php      # Staff management
│   └── reports.php           # Reports and analytics
├── staff/
│   ├── dashboard.php         # Staff dashboard
│   ├── create_booking.php    # Create booking
│   ├── confirm_booking.php   # Confirm booking
│   ├── return_vehicle.php    # Process return
│   └── record_payment.php    # Record payment
├── customer/
│   ├── dashboard.php         # Customer dashboard
│   ├── book_vehicle.php      # Book vehicle
│   ├── my_bookings.php       # View bookings
│   ├── payment.php           # Make payment
│   └── profile.php           # Manage profile
├── assets/
│   ├── css/
│   │   └── style.css         # Custom CSS styles
│   └── js/
│       └── script.js         # JavaScript utilities
├── database/
│   └── vehicle_rental.sql    # Database schema
├── index.php                 # Home page
├── login.php                 # Login page
├── register.php              # Registration page
├── logout.php                # Logout handler
└── README.txt                # This file
```

## Database Schema

### Users Table
- id, name, email, password, phone, license_number, role, timestamps

### Vehicles Table
- id, make, model, year, license_plate, daily_rate, status, timestamps

### Bookings Table
- id, customer_id, vehicle_id, start_date, end_date, total_cost, status, timestamps

### Payments Table
- id, booking_id, amount, payment_method, status, timestamps

### Maintenance Records Table
- id, vehicle_id, issue_description, maintenance_date, completion_date, cost, status, timestamps

## Security Features

- Password Hashing (PHP password_hash)
- SQL Injection Prevention (Prepared Statements)
- CSRF Protection (Session-based)
- Role-based Access Control (RBAC)
- Input Validation and Sanitization
- XSS Prevention (htmlspecialchars)

## API Endpoints

All operations are form-based. API endpoints can be created by extending the system.

## Usage Examples

### Creating a Booking (Customer)
1. Navigate to "Book a Vehicle"
2. Select vehicle, start date, and end date
3. Review estimated cost
4. Submit booking request

### Confirming a Booking (Staff)
1. Go to "Confirm Booking" in staff dashboard
2. Review pending bookings
3. Click "Confirm" to approve booking

### Recording a Payment (Staff)
1. Navigate to "Record Payment"
2. Select booking
3. Choose payment method
4. Submit payment

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in config/database.php
- Ensure database exists

### Login Issues
- Clear browser cookies
- Check user exists in database
- Verify password is correct

### File Upload Issues
- Check file permissions (755 for directories)
- Verify uploads directory exists
- Check PHP upload_max_filesize setting

## Features to Add (Future Development)

- Email notifications
- SMS alerts
- Damage reports
- Insurance management
- Customer reviews
- Mobile app
- Payment gateway integration
- Advanced reporting
- API REST endpoints

## Support

For issues or questions, please create an issue on GitHub or contact support@vehiclerental.com

## License

This project is licensed under the MIT License - see LICENSE file for details

## Author

Vehicle Rental System
Year: 2026
Version: 1.0.0

## Changelog

### Version 1.0.0 (2026-03-04)
- Initial release
- Core functionality implemented
- Admin, Staff, and Customer modules
- Payment system
- Booking management