# Daily-Expensive-Tracker
## Overview
The **Daily Expense Tracker** is a web-based application that helps users track their daily expenses efficiently. Users can log their expenditures, categorize them, and analyze their spending habits over time. The application provides a user-friendly interface along with data visualization to enhance financial awareness.

## Features
- User authentication and secure login/logout system.
- Add, edit, and delete expense entries.
- Categorize expenses (e.g., food, transportation, entertainment, bills, etc.).
- View expenses in a tabular format with sorting and filtering options.
- Generate summary reports and visual analytics.
- Responsive design for mobile and desktop devices.

## Technologies Used
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL

## Installation and Setup
### Prerequisites
- Web server (XAMPP for local development)
- PHP installed
- MySQL database

### Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/daily-expense-tracker.git
   cd daily-expense-tracker
   ```
2. Import the database:
   - Create a database in MySQL (e.g., `expense_tracker`).
   - Import `database.sql` file located in the project folder.
3. Configure database connection:
   - Open `config.php` and update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'expense_tracker');
     ```
4. Start the server:
   - If using XAMPP, start Apache and MySQL.
   - Place the project folder in the `htdocs` directory.
   - Access the application via `http://localhost/daily-expense-tracker/` in your browser.

## Usage
1. Register for an account or log in.
2. Add new expense records with details (amount, category, description, date).
3. View, edit, or delete existing expenses.
4. Analyze spending trends using charts and reports.

## Contributing
Feel free to fork this repository and submit pull requests. Suggestions and improvements are welcome!


