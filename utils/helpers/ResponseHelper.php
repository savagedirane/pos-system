<?php
/**
 * File: ResponseHelper.php
 * Description: API response formatting helper
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

class ResponseHelper {
    
    /**
     * Send success response
     * @param mixed $data
     * @param int $code
     * @param string $message
     */
    public static function success($data = null, $code = 200, $message = 'Operation successful') {
        self::send([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d\TH:i:s\Z')
        ], $code);
    }

    /**
     * Send error response
     * @param string $message
     * @param int $code
     * @param mixed $errors
     */
    public static function error($message = 'Operation failed', $code = 400, $errors = null) {
        self::send([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d\TH:i:s\Z')
        ], $code);
    }

    /**
     * Send validation error response
     * @param array $errors
     */
    public static function validationError($errors) {
        self::send([
            'status' => 'error',
            'code' => 422,
            'message' => 'Validation failed',
            'errors' => $errors,
            'timestamp' => date('Y-m-d\TH:i:s\Z')
        ], 422);
    }

    /**
     * Send paginated response
     * @param array $data
     * @param int $total
     * @param int $page
     * @param int $per_page
     */
    public static function paginated($data, $total, $page = 1, $per_page = 20) {
        self::send([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per_page,
                'pages' => ceil($total / $per_page)
            ],
            'timestamp' => date('Y-m-d\TH:i:s\Z')
        ], 200);
    }

    /**
     * Send JSON response
     * @param array $response
     * @param int $code
     */
    private static function send($response, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect to URL
     * @param string $url
     * @param int $code
     */
    public static function redirect($url, $code = 302) {
        header("Location: {$url}", true, $code);
        exit;
    }

    /**
     * Download file
     * @param string $file_path
     * @param string $file_name
     */
    public static function download($file_path, $file_name = null) {
        if (!file_exists($file_path)) {
            self::error('File not found', 404);
        }

        $file_name = $file_name ?? basename($file_path);
        $file_size = filesize($file_path);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . $file_size);
        header('Cache-Control: public');

        readfile($file_path);
        exit;
    }
}
?>
