**list_expenses.php**

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
    <title>Expenses Management</title>
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
        .table-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container table th, .table-container table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
        }
        .table-container table th {
            background-color: #f0f0f0;
        }
        .search-bar {
            padding: 1rem;
            background-color: #f7f7f7;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
        }
        .search-bar input[type="search"] {
            width: 100%;
            padding: 0.5rem;
            border: none;
            border-radius: 0.25rem;
        }
        .search-bar input[type="search"]:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">Back to Index</a>
        <span class="text-indigo-500">Welcome, <?php echo $_SESSION['username']; ?></span>
        <a href="logout.php">Logout</a>
    </div>
    <div class="table-container">
        <div class="search-bar">
            <input type="search" id="search-input" placeholder="Search...">
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php
                // Fetch records from backend
                $url = '../backend/expenses.php';
                $response = file_get_contents($url);
                $data = json_decode($response, true);
                foreach ($data as $record) {
                    ?>
                    <tr>
                        <td><?php echo $record['id']; ?></td>
                        <td><?php echo $record['description']; ?></td>
                        <td><?php echo $record['amount']; ?></td>
                        <td>
                            <a href="edit_expenses.php?id=<?php echo $record['id']; ?>">Edit</a>
                            <button class="btn btn-danger" onclick="deleteRecord(<?php echo $record['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <button class="btn btn-primary" onclick="location.href='create_expenses.php'">Add New Item</button>

    <script>
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('table-body');

        searchInput.addEventListener('input', () => {
            const searchValue = searchInput.value.toLowerCase();
            const tableRows = tableBody.children;
            for (let i = 0; i < tableRows.length; i++) {
                const row = tableRows[i];
                const descriptionCell = row.children[1];
                const descriptionText = descriptionCell.textContent.toLowerCase();
                if (descriptionText.includes(searchValue)) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            }
        });

        function deleteRecord(id) {
            fetch('../backend/expenses.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Record deleted successfully!');
                    location.reload();
                } else {
                    alert('Error deleting record!');
                }
            })
            .catch(error => console.error(error));
        }
    </script>
</body>
</html>

**expenses.php (backend)**


<?php
// Fetch records from database
$records = array();
// Replace with your database connection and query
// For demonstration purposes, we'll use a hardcoded array
$records = array(
    array('id' => 1, 'description' => 'Rent', 'amount' => 1000),
    array('id' => 2, 'description' => 'Groceries', 'amount' => 500),
    array('id' => 3, 'description' => 'Utilities', 'amount' => 200)
);

// Return records as JSON
header('Content-Type: application/json');
echo json_encode($records);
?>

This code assumes you have a `expenses.php` file in the `backend` directory that fetches records from your database and returns them as JSON. You'll need to replace the hardcoded array in `expenses.php` with your actual database query.