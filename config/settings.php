<?php
/**
 * File: settings.php
 * Description: Application settings and configuration
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

// Directory Settings - Only define if not already defined
if (!defined('APP_DIR')) {
    define('APP_DIR', dirname(dirname(__FILE__)));
}

// Application Settings
define('APP_TIMEZONE', 'UTC');
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Path Settings
define('UPLOAD_DIR', APP_DIR . '/public/uploads/');
define('LOG_DIR', APP_DIR . '/logs/');
define('TEMP_DIR', APP_DIR . '/temp/');

// Security Settings
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_OPTIONS', ['cost' => 12]);
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900); // 15 minutes

// Feature Flags
define('ENABLE_CHATBOT', true);
define('ENABLE_REPORTS', true);
define('ENABLE_EMAIL_NOTIFICATIONS', false);
define('ENABLE_SMS_NOTIFICATIONS', false);

// Pagination
define('ITEMS_PER_PAGE', 20);
define('DEFAULT_PAGE', 1);

// Currency Settings
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_CODE', 'USD');

// API Settings
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per minute
define('API_TIMEOUT', 30); // seconds

// Chatbot Settings
return [
    'chatbot' => [
        'enabled' => ENABLE_CHATBOT,
        'provider' => 'openai', // options: openai, dialogflow, rasa
        'openai' => [
            'api_key' => getenv('OPENAI_API_KEY'),
            'model' => 'gpt-3.5-turbo',
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ],
        'max_context_messages' => 10,
        'response_timeout' => 30,
    ],
    
    'email' => [
        'enabled' => ENABLE_EMAIL_NOTIFICATIONS,
        'smtp_host' => 'localhost',
        'smtp_port' => 587,
        'smtp_user' => '',
        'smtp_password' => '',
        'from_email' => 'noreply@possystem.local',
        'from_name' => 'POS System',
    ],
    
    'logging' => [
        'level' => 'INFO', // DEBUG, INFO, WARNING, ERROR, CRITICAL
        'max_file_size' => 10485760, // 10MB
        'backup_files' => 5,
        'log_to_file' => true,
        'log_to_database' => true,
    ],
];

// Set default timezone
date_default_timezone_set(APP_TIMEZONE);
?>
