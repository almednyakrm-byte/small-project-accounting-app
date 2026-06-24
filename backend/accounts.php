<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Define routes
$routes = [
    '/accounts' => [
        'GET' => function() {
            // Select all accounts
            $stmt = $pdo->prepare('SELECT * FROM accounts');
            $stmt->execute();
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($accounts);
        },
        'POST' => function() {
            // Validate input
            if (!isset($input['name']) || !isset($input['email']) || !isset($input['role'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input']);
                exit;
            }

            // Sanitize input
            $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
            $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
            $role = filter_var($input['role'], FILTER_SANITIZE_STRING);

            // Insert new account
            $stmt = $pdo->prepare('INSERT INTO accounts (name, email, role) VALUES (:name, :email, :role)');
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            $stmt->execute();

            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Account created successfully']);
        },
    ],
    '/accounts/{id}' => [
        'GET' => function($id) {
            // Select account by ID
            $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$account) {
                http_response_code(404);
                echo json_encode(['error' => 'Account not found']);
                exit;
            }

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($account);
        },
        'PUT' => function($id) {
            // Validate input
            if (!isset($input['name']) || !isset($input['email']) || !isset($input['role'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input']);
                exit;
            }

            // Sanitize input
            $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
            $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
            $role = filter_var($input['role'], FILTER_SANITIZE_STRING);

            // Check if user is admin
            if ($_SESSION['user_role'] !== 'admin') {
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                exit;
            }

            // Update account
            $stmt = $pdo->prepare('UPDATE accounts SET name = :name, email = :email, role = :role WHERE id = :id');
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Account updated successfully']);
        },
        'DELETE' => function($id) {
            // Check if user is admin
            if ($_SESSION['user_role'] !== 'admin') {
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                exit;
            }

            // Delete account
            $stmt = $pdo->prepare('DELETE FROM accounts WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Account deleted successfully']);
        },
    ],
];

// Get route and method
$method = $_SERVER['REQUEST_METHOD'];
$route = $_SERVER['REQUEST_URI'];

// Parse route parameters
$parts = explode('/', $route);
$parts = array_filter($parts);
$route = implode('/', $parts);

// Check if route exists
if (!isset($routes[$route])) {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
    exit;
}

// Get route handler
$handler = $routes[$route][$method];

// Call route handler
if (is_callable($handler)) {
    $handler(...array_slice($parts, 1));
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}