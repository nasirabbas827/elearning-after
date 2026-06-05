# elearning_after_prototype  

A lightweight PHP prototype for an e‑learning platform that demonstrates core functionalities such as user management, course handling, and PDF report generation using the **FPDF** library.

---  

## Overview  

`elearning_after_prototype` builds on an earlier proof‑of‑concept and provides a more complete back‑end implementation. The repository contains:

| Path | Description |
|------|-------------|
| `Database/elearning.sql` | MySQL dump with the required schema and sample data. |
| `FPDF/` | Bundled **FPDF** library (v1.81) with documentation, changelog and Composer configuration. |
| `FPDF/README.md` | Quick start guide for the FPDF library. |
| `FPDF/doc/*.htm` | HTML documentation for each FPDF method (e.g., `addpage.htm`, `cell.htm`). |

The application is ready to be cloned, the database imported, and run on any PHP 7.4+ environment.

---  

## Features  

- **Course & Lesson Management** – CRUD operations for courses, modules, and lessons.  
- **User Authentication** – Simple session‑based login (email / password).  
- **Progress Tracking** – Store and retrieve user progress per lesson.  
- **PDF Generation** – Export course summaries, certificates, and quiz results using FPDF.  
- **Database Seed** – Pre‑populated tables (users, courses, lessons) for quick testing.  

---  

## Tech Stack  

| Layer | Technology |
|-------|------------|
| Language | PHP (≥ 7.4) |
| Database | MySQL / MariaDB |
| PDF Engine | FPDF (bundled) |
| Dependency Management | Composer (optional for FPDF) |
| Web Server | Apache / Nginx (any LAMP stack) |

---  

## Installation  

1. **Clone the repository**  

   ```bash
   git clone https://github.com/yourusername/elearning_after_prototype.git
   cd elearning_after_prototype
   ```

2. **Create a MySQL database**  

   ```sql
   CREATE DATABASE elearning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import the schema & sample data**  

   ```bash
   mysql -u your_user -p elearning < Database/elearning.sql
   ```

4. **Configure the application**  

   - Copy `config.sample.php` to `config.php` (if a sample file exists) or edit the connection settings in `src/db.php` (or wherever the DB connection is defined).  
   - Example configuration snippet:  

     ```php
     <?php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'elearning');
     define('DB_USER', 'YOUR_DB_USER');
     define('DB_PASS', 'YOUR_DB_PASSWORD');
     ?>
     ```

5. **(Optional) Install Composer dependencies**  

   ```bash
   cd FPDF
   composer install
   ```

6. **Set up the web server**  

   - Point the document root to the repository’s `public/` (or the folder that contains `index.php`).  
   - Ensure the `uploads/` directory (if any) is writable by the web server.

---  

## Usage  

### Running the prototype  

1. Open your browser and navigate to the configured host, e.g.:

   ```
   http://localhost/elearning_after_prototype/
   ```

2. Log in with one of the seeded accounts (see `Database/elearning.sql` for usernames/passwords).  

3. Explore the