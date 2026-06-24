<?php
require_once 'db.php';

// Get user role and ID from session
$userRole = $_SESSION['userRole'];
$userID = $_SESSION['userID'];

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if user is logged in
    if (!$userRole) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Get all expenses
    $stmt = $pdo->prepare('SELECT * FROM expenses');
    $stmt->execute();
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return expenses in JSON format
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($expenses);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!$userRole) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Get input data
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Validate input data
    if (!isset($inputData['amount']) || !isset($inputData['description'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // Sanitize input data
    $amount = (float) $inputData['amount'];
    $description = trim($inputData['description']);

    // Insert new expense
    $stmt = $pdo->prepare('INSERT INTO expenses (amount, description, user_id) VALUES (:amount, :description, :user_id)');
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':user_id', $userID);
    $stmt->execute();

    // Return new expense in JSON format
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Expense created successfully']);
}

// Handle PUT request
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Check if user is logged in and has admin role
    if (!$userRole || $userRole !== 'admin') {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Get input data
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Validate input data
    if (!isset($inputData['id']) || !isset($inputData['amount']) || !isset($inputData['description'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // Sanitize input data
    $id = (int) $inputData['id'];
    $amount = (float) $inputData['amount'];
    $description = trim($inputData['description']);

    // Update expense
    $stmt = $pdo->prepare('UPDATE expenses SET amount = :amount, description = :description WHERE id = :id');
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Return updated expense in JSON format
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Expense updated successfully']);
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check if user is logged in and has admin role
    if (!$userRole || $userRole !== 'admin') {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Get input data
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Validate input data
    if (!isset($inputData['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // Sanitize input data
    $id = (int) $inputData['id'];

    // Delete expense
    $stmt = $pdo->prepare('DELETE FROM expenses WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Return deleted expense in JSON format
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Expense deleted successfully']);
}