# Database Setup

Run the following SQL in your MySQL client (e.g., phpMyAdmin or command line) to set up the database.

```sql
CREATE DATABASE IF NOT EXISTS lovecrafted;
USE lovecrafted;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    premium TINYINT(1) DEFAULT 0,
    oauth_provider VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
