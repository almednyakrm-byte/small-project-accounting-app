**edit_projects.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get project ID from URL
$id = $_GET['id'];

// Fetch project details via AJAX
$project = json_decode(file_get_contents('../backend/projects.php?id=' . $id), true);

// Check if project exists
if (empty($project)) {
    echo 'Project not found.';
    exit;
}

// Set page title and mod slug
$page_title = 'Edit Project';
$mod_slug = 'projects';

// Include header and navigation
include 'header.php';
?>

<!-- Edit Project Form -->
<div class="max-w-md mx-auto p-8 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-bold text-slate-900 mb-4"><?= $page_title ?></h2>
    <form id="edit-project-form">
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-slate-900">Title</label>
            <input type="text" id="title" name="title" class="block w-full p-2 mt-1 text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" value="<?= $project['title'] ?>">
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-slate-900">Description</label>
            <textarea id="description" name="description" class="block w-full p-2 mt-1 text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"><?= $project['description'] ?></textarea>
        </div>
        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Save Changes</button>
    </form>
</div>

<!-- JavaScript to fetch project details and handle form submission -->
<script>
    // Fetch project details via GET
    fetch('../backend/projects.php?id=<?= $id ?>')
        .then(response => response.json())
        .then(data => {
            // Populate form fields
            document.getElementById('title').value = data.title;
            document.getElementById('description').value = data.description;
        })
        .catch(error => console.error(error));

    // Handle form submission via AJAX PUT request
    document.getElementById('edit-project-form').addEventListener('submit', event => {
        event.preventDefault();
        const formData = new FormData(event.target);
        fetch('../backend/projects.php', {
            method: 'PUT',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'list_<?= $mod_slug ?>.php';
                } else {
                    console.error(data.error);
                }
            })
            .catch(error => console.error(error));
    });
</script>

<?php
// Include footer
include 'footer.php';
?>


**projects.php (backend)**

<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

// Get project ID from URL
$id = $_GET['id'];

// Fetch project details from database
$project = get_project($id);

// Return project details as JSON
if ($project) {
    echo json_encode($project);
} else {
    http_response_code(404);
    echo 'Project not found.';
}

// Function to get project details from database
function get_project($id) {
    // Database connection code here
    // ...
    // Return project details
    return array(
        'id' => $id,
        'title' => 'Project Title',
        'description' => 'Project Description'
    );
}
?>


**header.php and footer.php (not included in this response)**

Note: This code assumes you have a `projects.php` file in the `backend` directory that handles the GET and PUT requests. You'll need to modify the `get_project` function to retrieve project details from your database. Additionally, you'll need to include the `header.php` and `footer.php` files to complete the layout.