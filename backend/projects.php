<?php

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Define routes
$routes = array(
    'GET' => array('/projects', 'getProjects'),
    'POST' => array('/projects', 'createProject'),
    'PUT' => array('/projects/:id', 'updateProject'),
    'DELETE' => array('/projects/:id', 'deleteProject')
);

// Get route
$method = $_SERVER['REQUEST_METHOD'];
$route = explode('/', $_SERVER['REQUEST_URI']);
array_shift($route); // Remove empty string
array_shift($route); // Remove 'projects'

// Check if route exists
if (!isset($routes[$method])) {
    http_response_code(405);
    echo json_encode(array('error' => 'Method Not Allowed'));
    exit;
}

// Get route parameters
$parameters = array();
foreach ($route as $param) {
    if (strpos($param, ':') === 0) {
        $parameters[$param] = $param;
    }
}

// Get user role
$userRole = $_SESSION['user_role'];

// Call route handler
$handler = $routes[$method][1];
$result = call_user_func_array(array($this, $handler), array($input, $parameters, $userRole));

// Output result
http_response_code($result['status']);
header('Content-Type: application/json');
echo json_encode($result['data']);

// Route handlers
function getProjects($input, $parameters, $userRole) {
    // Validate input
    if (!isset($input['limit']) || !isset($input['offset'])) {
        return array('status' => 400, 'data' => array('error' => 'Invalid input'));
    }

    // Sanitize input
    $limit = (int) $input['limit'];
    $offset = (int) $input['offset'];

    // Prepare SQL query
    $sql = 'SELECT * FROM projects LIMIT :limit OFFSET :offset';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch results
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return result
    return array('status' => 200, 'data' => $projects);
}

function createProject($input, $parameters, $userRole) {
    // Validate input
    if (!isset($input['name']) || !isset($input['description'])) {
        return array('status' => 400, 'data' => array('error' => 'Invalid input'));
    }

    // Sanitize input
    $name = $pdo->quote($input['name']);
    $description = $pdo->quote($input['description']);

    // Prepare SQL query
    $sql = 'INSERT INTO projects (name, description) VALUES (:name, :description)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->execute();

    // Return result
    return array('status' => 201, 'data' => array('message' => 'Project created successfully'));
}

function updateProject($input, $parameters, $userRole) {
    // Validate input
    if (!isset($input['name']) || !isset($input['description'])) {
        return array('status' => 400, 'data' => array('error' => 'Invalid input'));
    }

    // Sanitize input
    $id = (int) $parameters['id'];
    $name = $pdo->quote($input['name']);
    $description = $pdo->quote($input['description']);

    // Prepare SQL query
    $sql = 'UPDATE projects SET name = :name, description = :description WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->execute();

    // Check if user is admin
    if ($userRole !== 'admin') {
        return array('status' => 403, 'data' => array('error' => 'Forbidden'));
    }

    // Return result
    return array('status' => 200, 'data' => array('message' => 'Project updated successfully'));
}

function deleteProject($input, $parameters, $userRole) {
    // Validate input
    if (!isset($input['id'])) {
        return array('status' => 400, 'data' => array('error' => 'Invalid input'));
    }

    // Sanitize input
    $id = (int) $input['id'];

    // Prepare SQL query
    $sql = 'DELETE FROM projects WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Check if user is admin
    if ($userRole !== 'admin') {
        return array('status' => 403, 'data' => array('error' => 'Forbidden'));
    }

    // Return result
    return array('status' => 200, 'data' => array('message' => 'Project deleted successfully'));
}

?>