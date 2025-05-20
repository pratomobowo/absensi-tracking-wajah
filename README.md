# Face Recognition Attendance System

A Laravel-based employee attendance system using face recognition technology. This system allows for contactless attendance tracking, employee management, and attendance reporting.

## Features

- Face recognition for employee attendance
- Employee management (add, edit, delete)
- Admin dashboard with attendance reports
- Real-time attendance tracking
- Secure authentication

## Requirements

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL
- Web camera for face recognition

## Installation

1. Clone the repository
```bash
git clone https://github.com/pratomobowo/absensi-tracking-wajah.git
```

2. Navigate to the project directory
```bash
cd absensi-tracking-wajah
```

3. Install PHP dependencies
```bash
composer install
```

4. Install JavaScript dependencies
```bash
npm install
```

5. Create and configure your .env file
```bash
cp .env.example .env
php artisan key:generate
```

6. Configure your database in the .env file

7. Run migrations
```bash
php artisan migrate
```

8. Compile assets
```bash
npm run build
```

9. Start the development server
```bash
php artisan serve
```

## Usage

1. Access the admin panel to add employees and their face data
2. Employees can use the attendance page to check in/out using face recognition
3. Admin can generate reports and monitor attendance

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
