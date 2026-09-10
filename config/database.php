<?php
/**
 * NOVA TECH - Database Connection (PDO)
 * 
 * This file creates a PDO database connection using the config values.
 * PDO (PHP Data Objects) is the modern, secure way to connect to databases in PHP.
 * 
 * WHY PDO?
 * - Supports multiple database types (MySQL, PostgreSQL, SQLite, etc.)
 * - Uses prepared statements to prevent SQL injection
 * - Better error handling than old mysqli functions
 * 
 * HOW IT'S USED:
 *   require_once 'config/database.php';
 *   // $pdo is now available as a database connection
 */

require_once __DIR__ . '/config.php';

try {
    // Build the Data Source Name (DSN) - tells PDO which database to connect to
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    // PDO connection options
    $options = [
        // Throw exceptions on errors (instead of silent failures)
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        // Return results as associative arrays (e.g., $row['name'])
        // instead of numbered arrays (e.g., $row[0])
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // Use real prepared statements (not emulated)
        // This provides better SQL injection protection
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    // Create the PDO connection
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // If connection fails, show a friendly error message
    // In production, you would log this error instead of displaying it
    http_response_code(500);
    die("Database connection failed. Please check your configuration.<br>Error: " . $e->getMessage());
}
