<?php
/**
 * API Entry Point
 * Main API endpoint that routes requests to appropriate controllers
 * 
 * This file has been refactored to use a modular backend architecture:
 * - Controllers handle request/response logic
 * - Models manage data operations
 * - Helpers provide utility functions
 * - Router manages endpoint routing
 */

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Bootstrap the application
require_once __DIR__ . '/backend/bootstrap.php';

// Initialize the application (create directories, default files, etc.)
initializeApplication();

// Initialize routes
Router::initializeRoutes();

// Get the requested action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Route the request
Router::handle($action);
