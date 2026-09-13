<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Database.php';

$routes = require __DIR__ . '/../routes/api.php';

$database = new Database();
$db = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$route = null;
$params = [];

foreach ($routes[$method] ?? [] as $routePattern => $handler) {

    if (strpos($routePattern, '{id}') !== false) {

        $pattern = str_replace(
            '{id}',
            '([0-9]+)',
            $routePattern
        );

        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            $route = $handler;
            $params['id'] = $matches[1];
            break;
        }

    } else {

        // Route عادي بدون parameters
        if ($routePattern === $uri) {
            $route = $handler;
            break;
        }
    }
}

if ($route) {

    [$class, $methodName] = explode('@', $route);

    if (class_exists($class)) {

        $object = new $class($db);

        $response = empty($params)
            ? $object->$methodName()
            : $object->$methodName($params['id']);

        // http_response_code($response['status']);
        $statusCode = $response['status'] ?? ($response['success'] ? 200 : 400);

        http_response_code($statusCode);
        echo json_encode($response);

        exit();
    }
}
http_response_code(404);

echo json_encode([
    'message' => 'Route not found'
]);