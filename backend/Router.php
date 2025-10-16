<?php
/**
 * API Router
 * Handles routing of API requests to appropriate controllers
 */

class Router {
    private static $routes = [];

    /**
     * Register a route
     */
    public static function register($action, $controller, $method) {
        self::$routes[$action] = ['controller' => $controller, 'method' => $method];
    }

    /**
     * Handle incoming request
     */
    public static function handle($action) {
        if (empty($action) || !isset(self::$routes[$action])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            return;
        }

        $route = self::$routes[$action];
        $controller = $route['controller'];
        $method = $route['method'];

        // Call the controller method
        call_user_func([$controller, $method]);
    }

    /**
     * Initialize all routes
     */
    public static function initializeRoutes() {
        // Schedule routes
        self::register('getSchedule', 'ScheduleController', 'getSchedule');
        self::register('saveSchedule', 'ScheduleController', 'saveSchedule');

        // Settings routes
        self::register('getSettings', 'SettingsController', 'getSettings');
        self::register('saveSettings', 'SettingsController', 'saveSettings');
        self::register('getPinCode', 'SettingsController', 'getPinCode');

        // Session routes
        self::register('registerSession', 'SessionController', 'registerSession');
        self::register('checkSession', 'SessionController', 'checkSession');
        self::register('getSessions', 'SessionController', 'getSessions');
        self::register('disconnectUser', 'SessionController', 'disconnectUser');

        // Audit routes
        self::register('getAuditLog', 'AuditController', 'getAuditLog');
    }
}
