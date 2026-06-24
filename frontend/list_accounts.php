**list_accounts.php**

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
    <title>Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .header {
            background-color: #2d3748;
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
            background-color: #2d3748;
            color: #fff;
        }
        .search-bar {
            padding: 1rem;
            background-color: #f7f7f7;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
        }
        .search-bar input[type="search"] {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.2rem;
        }
        .search-bar input[type="search"]:focus {
            outline: none;
            box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.25);
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
            <h2 class="text-2xl font-bold text-slate-900">Accounts</h2>
            <a href="create_accounts.php" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Add New Item</a>
        </div>
        <div class="search-bar">
            <input type="search" id="search-input" placeholder="Search...">
            <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" id="search-button">Search</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php
                // Fetch data from backend
                $response = file_get_contents('../backend/accounts.php');
                $data = json_decode($response, true);
                foreach ($data as $row) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <a href="edit_accounts.php?id=<?php echo $row['id']; ?>" class="text-indigo-500 hover:text-indigo-700">Edit</a>
                            <button class="text-red-500 hover:text-red-700" onclick="deleteAccount(<?php echo $row['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-button');
        const tableBody = document.getElementById('table-body');

        searchButton.addEventListener('click', () => {
            const searchQuery = searchInput.value.trim();
            if (searchQuery !== '') {
                fetch('../backend/accounts.php?search=' + searchQuery)
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = '';
                        data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.id}</td>
                                <td>${row.name}</td>
                                <td>${row.email}</td>
                                <td>
                                    <a href="edit_accounts.php?id=${row.id}" class="text-indigo-500 hover:text-indigo-700">Edit</a>
                                    <button class="text-red-500 hover:text-red-700" onclick="deleteAccount(${row.id})">Delete</button>
                                </td>
                            `;
                            tableBody.appendChild(tr);
                        });
                    });
            } else {
                fetch('../backend/accounts.php')
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = '';
                        data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.id}</td>
                                <td>${row.name}</td>
                                <td>${row.email}</td>
                                <td>
                                    <a href="edit_accounts.php?id=${row.id}" class="text-indigo-500 hover:text-indigo-700">Edit</a>
                                    <button class="text-red-500 hover:text-red-700" onclick="deleteAccount(${row.id})">Delete</button>
                                </td>
                            `;
                            tableBody.appendChild(tr);
                        });
                    });
            }
        });

        // Delete account functionality
        function deleteAccount(id) {
            if (confirm('Are you sure you want to delete this account?')) {
                fetch('../backend/accounts.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Account deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error deleting account!');
                    }
                });
            }
        }
    </script>
</body>
</html>

**accounts.php (backend)**

<?php
// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search functionality
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $query = "SELECT * FROM accounts WHERE name LIKE '%$searchQuery%' OR email LIKE '%$searchQuery%'";
} else {
    $query = "SELECT * FROM accounts";
}

// Execute query
$result = $conn->query($query);

// Fetch data
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Close connection
$conn->close();

// Output data
echo json_encode($data);
?>

Note: This is a basic implementation and you should modify it according to your specific requirements and security considerations.