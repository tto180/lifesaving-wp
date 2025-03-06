<?php
/**
 * Security Helper Functions
 * Provides a centralized way to sanitize input, prevent SQL injection, secure emails, escape CSV data, and log errors.
 */

/**
 * Sanitizes user input using PHP's `filter_input()`.
 *
 * @param int $type Type of input source (e.g., INPUT_GET, INPUT_POST)
 * @param string $key The name of the input variable
 * @param int $filter The filter type (e.g., FILTER_SANITIZE_STRING, FILTER_VALIDATE_EMAIL)
 * @param mixed $default Default value if input is missing or invalid
 * @return mixed Sanitized input value or default value
 */
function sanitize_input($type, $key, $filter, $default = null) {
    $value = filter_input($type, $key, $filter);
    return $value !== false && $value !== null ? $value : $default;
}

/**
 * Sanitizes a string for safe output (XSS Protection).
 *
 * @param string $data User input string
 * @return string Sanitized string with HTML entities converted
 */
function sanitize_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Escapes input data for SQL queries to prevent SQL Injection.
 * Use this if you're using legacy MySQLi or unsafe queries.
 *
 * @param mysqli $conn The MySQLi connection object
 * @param string $data The input data to escape
 * @return string Escaped string
 */
function escape_sql($conn, $data) {
    return mysqli_real_escape_string($conn, $data);
}

/**
 * Validates and sanitizes an email to prevent email injection attacks.
 *
 * @param string $email Email address
 * @return string|null Valid email or null if invalid
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
}

/**
 * Sanitizes CSV data to prevent formula injection attacks.
 * Adds a single quote (') before potentially dangerous characters.
 *
 * @param string $data The CSV cell data
 * @return string Sanitized CSV cell data
 */
function sanitize_csv($data) {
    return (strpos($data, '=') === 0 || strpos($data, '+') === 0 || strpos($data, '-') === 0) ? "'".$data : $data;
}

/**
 * Logs errors to a secure file.
 *
 * @param string $error_message The error message to log
 */
function log_error($error_message) {
    error_log("[SECURITY] " . date('Y-m-d H:i:s') . " - " . $error_message . "\n", 3, __DIR__ . "/security-errors.log");
}
?>
