**list_projects.php**

<?php
// Session validation
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .header {
            background-color: #1a1d23;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        .header a {
            color: #fff;
            text-decoration: none;
        }
        .header a:hover {
            color: #ccc;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 1rem;
        }
        .table th {
            background-color: #1a1d23;
            color: #fff;
        }
        .search-bar {
            width: 50%;
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
        }
        .search-bar input[type="search"] {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 0.5rem;
        }
        .search-bar input[type="search"]:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">Back to Index</a>
        <span class="text-indigo-500 font-bold">Welcome, <?php echo $_SESSION['username']; ?></span>
        <a href="logout.php" class="text-red-500">Logout</a>
    </div>
    <div class="container mx-auto p-4">
        <div class="flex justify-between mb-4">
            <h1 class="text-3xl font-bold text-slate-900">Projects</h1>
            <a href="create_projects.php" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Add New Item</a>
        </div>
        <div class="flex justify-between mb-4">
            <input type="search" class="search-bar" placeholder="Search..." id="search-input">
            <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" id="search-btn">Search</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="project-list">
                <!-- Project list will be populated here -->
            </tbody>
        </table>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const searchBtn = document.getElementById('search-btn');
        const projectList = document.getElementById('project-list');

        searchBtn.addEventListener('click', async () => {
            const searchQuery = searchInput.value.trim();
            if (searchQuery) {
                const response = await fetch(`../backend/projects.php?search=${searchQuery}`);
                const data = await response.json();
                projectList.innerHTML = '';
                data.forEach((project) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${project.id}</td>
                        <td>${project.name}</td>
                        <td>${project.description}</td>
                        <td>
                            <a href="edit_projects.php?id=${project.id}" class="text-indigo-500 hover:text-indigo-700">Edit</a>
                            <button class="text-red-500 hover:text-red-700" onclick="deleteProject(${project.id})">Delete</button>
                        </td>
                    `;
                    projectList.appendChild(row);
                });
            } else {
                projectList.innerHTML = '';
                const response = await fetch('../backend/projects.php');
                const data = await response.json();
                data.forEach((project) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${project.id}</td>
                        <td>${project.name}</td>
                        <td>${project.description}</td>
                        <td>
                            <a href="edit_projects.php?id=${project.id}" class="text-indigo-500 hover:text-indigo-700">Edit</a>
                            <button class="text-red-500 hover:text-red-700" onclick="deleteProject(${project.id})">Delete</button>
                        </td>
                    `;
                    projectList.appendChild(row);
                });
            }
        });

        async function deleteProject(id) {
            if (confirm('Are you sure you want to delete this project?')) {
                const response = await fetch(`../backend/projects.php?id=${id}`, { method: 'DELETE' });
                if (response.ok) {
                    alert('Project deleted successfully!');
                    window.location.reload();
                } else {
                    alert('Error deleting project!');
                }
            }
        }
    </script>
</body>
</html>


**projects.php (backend)**

<?php
// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Search query
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $query = "SELECT * FROM projects WHERE name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%'";
} else {
    $query = "SELECT * FROM projects";
}

// Fetch data
$result = $conn->query($query);
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Output data in JSON format
header('Content-Type: application/json');
echo json_encode($data);
?>


Note: Replace `'localhost'`, `'username'`, `'password'`, and `'database'` with your actual database credentials and name. Also, make sure to create a `projects` table in your database with columns `id`, `name`, and `description`.