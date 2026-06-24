<?php

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Define routes
$routes = array(
    '/incomes' => 'listIncomes',
    '/incomes' => 'createIncome',
    '/incomes/:id' => 'getIncome',
    '/incomes/:id' => 'updateIncome',
    '/incomes/:id' => 'deleteIncome'
);

// Route the request
$match = false;
foreach ($routes as $route => $function) {
    if (strpos($route, ':id') !== false) {
        if (preg_match('/^' . preg_quote($route, '/') . '$/', $_SERVER['REQUEST_URI'])) {
            $id = explode('/', $_SERVER['REQUEST_URI'])[count(explode('/', $_SERVER['REQUEST_URI'])) - 2];
            $input['id'] = $id;
            $match = true;
            break;
        }
    } else {
        if (preg_match('/^' . preg_quote($route, '/') . '$/', $_SERVER['REQUEST_URI'])) {
            $match = true;
            break;
        }
    }
}

if (!$match) {
    http_response_code(404);
    echo json_encode(array('error' => 'Not found'));
    exit;
}

// Call the corresponding function
$function = $routes[$_SERVER['REQUEST_URI']];
$function();

// Helper functions
function listIncomes() {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM incomes');
    $stmt->execute();
    $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($incomes);
}

function createIncome() {
    global $pdo;
    if (!isset($input['amount']) || !isset($input['description'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request'));
        exit;
    }
    $amount = filter_var($input['amount'], FILTER_VALIDATE_FLOAT);
    $description = filter_var($input['description'], FILTER_SANITIZE_STRING);
    $stmt = $pdo->prepare('INSERT INTO incomes (amount, description, user_id) VALUES (:amount, :description, :user_id)');
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    if ($stmt->execute()) {
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(array('message' => 'Income created successfully'));
    } else {
        http_response_code(500);
        echo json_encode(array('error' => 'Internal server error'));
    }
}

function getIncome() {
    global $pdo;
    $id = $input['id'];
    $stmt = $pdo->prepare('SELECT * FROM incomes WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $income = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($income) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($income);
    } else {
        http_response_code(404);
        echo json_encode(array('error' => 'Not found'));
    }
}

function updateIncome() {
    global $pdo;
    if (!isset($input['amount']) || !isset($input['description'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request'));
        exit;
    }
    $id = $input['id'];
    $amount = filter_var($input['amount'], FILTER_VALIDATE_FLOAT);
    $description = filter_var($input['description'], FILTER_SANITIZE_STRING);
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }
    $stmt = $pdo->prepare('UPDATE incomes SET amount = :amount, description = :description WHERE id = :id AND user_id = :user_id');
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    if ($stmt->execute()) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array('message' => 'Income updated successfully'));
    } else {
        http_response_code(500);
        echo json_encode(array('error' => 'Internal server error'));
    }
}

function deleteIncome() {
    global $pdo;
    $id = $input['id'];
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }
    $stmt = $pdo->prepare('DELETE FROM incomes WHERE id = :id AND user_id = :user_id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    if ($stmt->execute()) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array('message' => 'Income deleted successfully'));
    } else {
        http_response_code(500);
        echo json_encode(array('error' => 'Internal server error'));
    }
}

?>