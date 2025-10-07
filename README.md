# 🏦 Banking Management System

A simple, secure, and responsive web application built with **PHP** and **MySQL** for managing user accounts and facilitating secure fund transfers. Designed with separate **Admin** and **User** roles to demonstrate core banking functionalities.

---

## ✨ Features

This system provides a full suite of banking simulation features, ensuring a clean separation between administrative controls and user operations.

### Security & Roles
- **Role-Based Access:** Dedicated dashboards for Administrators and regular Users.
- **Secure Authentication:** Secure login and session management across the application.
- **Database Transactions (ACID):** Fund transfers are wrapped in database transactions to guarantee money is either successfully debited and credited, or rolled back completely in case of failure.

### Administrative Features (`admin_dashboard.php`)
- **User Management:** Add new users to the system (`add_user.php`).
- **View All Users:** Display a list of all accounts and their current balances (`users.php`).
- **Global History:** View a complete, chronological history of all transactions across all users (`history.php`).
- **Admin Transfers:** Ability to transfer money between any two accounts.

### User Features (`user_dashboard.php`)
- **Profile Management:** View and update personal profile details and check current account balance (`profile.php`).
- **Fund Transfer:** Easily transfer money to any other registered user (`transfer.php`).
- **Personal History:** View a filtered list of only their own incoming and outgoing transactions (`user_history.php`).

### UI/UX
- **Modern Design:** Clean, centered card layout with responsive styling for all screen sizes (managed via `styles.css`).
- **Modular Code:** Consistent header and footer implementation using `footer.php`.

---

## 🛠️ Getting Started

Follow these steps to set up and run the application on your local machine.

### Prerequisites
You need a working web server environment with PHP and MySQL support. Recommended setup:

- **PHP:** Version 7.4 or later  
- **MySQL:** Database server  
- **Web Server:** Apache or Nginx (bundled in packages like XAMPP, WAMP, or MAMP)

### Installation

1. **Download/Clone:** Download all project files from this repository.  
2. **Extract Files:** Place the extracted files in your web server's root directory:  
   - For **XAMPP**: `C:\xampp\htdocs\your_project_folder`  
   - For **WAMP**: `C:\wamp64\www\your_project_folder`  
   - For **MAMP (Mac)**: `/Applications/MAMP/htdocs/your_project_folder`  
3. **Start Services:** Ensure your web server (Apache) and database server (MySQL) are running.  

### ⚙️ Database Configuration

1. **Create Database:** Log into your MySQL server (via phpMyAdmin or command line) and create a new, empty database (e.g., `banking_db`).  
2. **Import Schema:** Locate the provided `.sql` file (e.g., `banking_db.sql`) and import it into the new database.  
3. **Update `db.php`:** Open `db.php` and update the connection details:

### 🚀 How to Run

After setting up the database, navigate to the following URL in your web browser: http://localhost/path/to/your/project/index.php


1. Start by registering a new user.  
2. To enable admin features, manually change the user’s role in the database from `'user'` to `'admin'`.

---

## 🔑 Application Access

| Role  | Initial Access Page | Key Functionality |
|-------|-------------------|------------------|
| Admin | `login.php`        | Full control over users, balances, and global history. |
| User  | `login.php`        | Personal transfers, balance viewing, and personal transaction history. |

---

## 👨‍💻 Developer & Credit

This system was conceived and designed to be a secure, modular PHP web application.  

© 2025 **Banking Management System** | Designed by [Abhishek Shah](https://github.com/abhishek-2006)

---

## 🤝 Open to Changes

This project is built on standard PHP, MySQL, and HTML/CSS, making it highly flexible. You can modify files to improve:

- **Security Enhancements:** Improve password hashing or input validation.  
- **Feature Expansion:** Add features like email notifications or transaction descriptions.  
- **UI/UX Refinements:** Enhance the look and responsiveness of the application.

If you have specific suggestions or bug reports, feel free to reach out!


