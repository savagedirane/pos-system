<?php
/**
 * File: LoggerHelper.php
 * Description: Logging utility for application event tracking
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

class LoggerHelper {
    private $log_dir = LOG_DIR;
    private $log_file;
    private $log_level;

    public function __construct() {
        // Create logs directory if it doesn't exist
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0755, true);
        }
        
        $this->log_file = $this->log_dir . 'application.log';
        $this->log_level = 'INFO';
    }

    /**
     * Log debug message
     * @param string $message
     * @param array $context
     */
    public function logDebug($message, $context = []) {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Log info message
     * @param string $message
     * @param array $context
     */
    public function logInfo($message, $context = []) {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log warning message
     * @param string $message
     * @param array $context
     */
    public function logWarning($message, $context = []) {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log error message
     * @param string $message
     * @param array $context
     */
    public function logError($message, $context = []) {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log critical message
     * @param string $message
     * @param array $context
     */
    public function logCritical($message, $context = []) {
        $this->log('CRITICAL', $message, $context);
    }

    /**
     * Main logging function
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $user_id = $_SESSION['user_id'] ?? 'anonymous';
        
        $log_entry = "[{$timestamp}] [{$level}] [{$user_id}] {$message}";
        
        if (!empty($context)) {
            $log_entry .= " " . json_encode($context);
        }
        
        $log_entry .= "\n";
        
        // Log to file
        file_put_contents($this->log_file, $log_entry, FILE_APPEND);
        
        // Log to database if configured
        if (defined('DB_LOG_ENABLED') && DB_LOG_ENABLED) {
            $this->logToDatabase($level, $message, $context);
        }
    }

    /**
     * Log to database
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function logToDatabase($level, $message, $context) {
        global $db;
        
        if (!$db) return;
        
        $user_id = $_SESSION['user_id'] ?? null;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $context_json = json_encode($context);
        
        $query = "INSERT INTO audit_logs (user_id, action, ip_address, created_at) 
                  VALUES (?, ?, ?, NOW())";
        
        $stmt = $db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('iss', $user_id, $message, $ip_address);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Rotate log file if it exceeds max size
     */
    public function rotateLogFile() {
        $max_size = 10485760; // 10MB
        
        if (file_exists($this->log_file) && filesize($this->log_file) > $max_size) {
            $backup_file = $this->log_dir . 'application_' . date('Y-m-d_H-i-s') . '.log';
            rename($this->log_file, $backup_file);
        }
    }

    /**
     * Get log file contents
     * @param int $lines
     * @return array
     */
    public function getLogContents($lines = 100) {
        if (!file_exists($this->log_file)) {
            return [];
        }
        
        $file_content = file_get_contents($this->log_file);
        $log_lines = explode("\n", trim($file_content));
        
        return array_slice($log_lines, -$lines);
    }

    /**
     * Clear log file
     */
    public function clearLog() {
        if (file_exists($this->log_file)) {
            file_put_contents($this->log_file, '');
        }
    }
}
?>
