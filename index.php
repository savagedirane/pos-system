<?php
/**
 * File: index.php
 * Description: Main application redirect to public index
 * This file simply redirects to the public directory
 */

// Redirect to public index page
header('Location: ./public/index.php', true, 301);
exit;

?>
