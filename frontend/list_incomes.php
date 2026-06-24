**list_incomes.php**

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
    <title>Incomes Management</title>
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
            color: #ffffff;
            text-decoration: none;
        }
        .header a:hover {
            color: #ffffff;
            text-decoration: underline;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 1rem;
            text-align: left;
        }
        .table th {
            background-color: #2d3748;
            color: #ffffff;
        }
        .search-bar {
            width: 50%;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">Back to Index</a>
        <span class="text-indigo-500">Welcome, <?php echo $_SESSION['username']; ?></span>
        <a href="logout.php" class="text-indigo-500">Logout</a>
    </div>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl text-slate-900 mb-4">Incomes Management</h1>
        <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_incomes.php'">Add New Item</button>
        <div class="flex justify-center mb-4">
            <input type="search" class="search-bar" id="search-input" placeholder="Search...">
            <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="searchIncomes()">Search</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="incomes-list">
                <?php
                // Fetch incomes list from backend
                $url = '../backend/incomes.php';
                $response = fetchIncomes($url);
                $incomes = json_decode($response, true);
                foreach ($incomes as $income) {
                    ?>
                    <tr>
                        <td><?php echo $income['id']; ?></td>
                        <td><?php echo $income['amount']; ?></td>
                        <td><?php echo $income['description']; ?></td>
                        <td>
                            <a href="edit_incomes.php?id=<?php echo $income['id']; ?>" class="text-indigo-500">Edit</a>
                            <button class="text-red-500" onclick="deleteIncome(<?php echo $income['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchIncomes() {
            const searchInput = document.getElementById('search-input').value;
            const url = '../backend/incomes.php?search=' + searchInput;
            fetchIncomes(url);
        }

        function deleteIncome(id) {
            if (confirm('Are you sure you want to delete this income?')) {
                fetch('../backend/incomes.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting income');
                    }
                })
                .catch(error => console.error(error));
            }
        }

        function fetchIncomes(url) {
            return fetch(url)
            .then(response => response.json())
            .then(data => {
                const incomesList = document.getElementById('incomes-list');
                incomesList.innerHTML = '';
                data.forEach(income => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${income.id}</td>
                        <td>${income.amount}</td>
                        <td>${income.description}</td>
                        <td>
                            <a href="edit_incomes.php?id=${income.id}" class="text-indigo-500">Edit</a>
                            <button class="text-red-500" onclick="deleteIncome(${income.id})">Delete</button>
                        </td>
                    `;
                    incomesList.appendChild(row);
                });
            })
            .catch(error => console.error(error));
        }
    </script>
</body>
</html>


**backend/incomes.php**

<?php
// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Search query
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM incomes WHERE amount LIKE '%$search%' OR description LIKE '%$search%'";
} else {
    $query = "SELECT * FROM incomes";
}

// Fetch incomes list
$result = $conn->query($query);
$incomes = array();
while ($row = $result->fetch_assoc()) {
    $incomes[] = $row;
}

// JSON encode incomes list
echo json_encode($incomes);

// Delete income
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_POST['id'];
    $query = "DELETE FROM incomes WHERE id = '$id'";
    $conn->query($query);
    echo json_encode(array('success' => true));
}

// Close database connection
$conn->close();
?>