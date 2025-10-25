<?php

// ------------------------------------------------------------------
// 1. CONNECTION PARAMETERS (REQUIRED: CHANGE THESE VALUES)
// ------------------------------------------------------------------
$host = 'localhost';          // Your database host
$db   = 'your_database_name'; // The name of your database
$user = 'your_db_username';   // Your database username
$pass = 'your_db_password';   // Your database password
$charset = 'utf8mb4';

// Data Source Name (DSN) string
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options for PDO
$options = [
    // Throw exceptions on error for better debugging
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Fetch results as an associative array by default
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Turn off emulation mode for better security
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// ------------------------------------------------------------------
// 2. ESTABLISH THE CONNECTION
// ------------------------------------------------------------------
try {
     // Create the PDO instance (the actual connection)
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Connection failed, show an error and stop execution
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// The $pdo variable is now your active database connection object.

?>