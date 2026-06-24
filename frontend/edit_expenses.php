**edit_expenses.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get expense ID from URL
if (!isset($_GET['id'])) {
    header('Location: list_expenses.php');
    exit;
}

$expense_id = $_GET['id'];

// Fetch existing expense record
$url = '../backend/expenses.php?id=' . $expense_id;
$response = file_get_contents($url);
$expense = json_decode($response, true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded shadow-md">
        <h1 class="text-2xl font-bold text-slate-900 mb-4">Edit Expense</h1>
        <form id="edit-expense-form">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-slate-900">Name:</label>
                <input type="text" id="name" name="name" class="block w-full p-2 mt-1 text-sm text-gray-900 border border-gray-300 rounded-md" value="<?= $expense['name'] ?>">
            </div>
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-slate-900">Amount:</label>
                <input type="number" id="amount" name="amount" class="block w-full p-2 mt-1 text-sm text-gray-900 border border-gray-300 rounded-md" value="<?= $expense['amount'] ?>">
            </div>
            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-slate-900">Date:</label>
                <input type="date" id="date" name="date" class="block w-full p-2 mt-1 text-sm text-gray-900 border border-gray-300 rounded-md" value="<?= $expense['date'] ?>">
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Update Expense</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-expense-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/expenses.php',
                    data: formData,
                    success: function(response) {
                        window.location.href = 'list_expenses.php';
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>


**expenses.php (backend)**

<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get expense ID from URL
if (!isset($_GET['id'])) {
    header('Location: list_expenses.php');
    exit;
}

$expense_id = $_GET['id'];

// Fetch existing expense record
$query = "SELECT * FROM expenses WHERE id = '$expense_id'";
$result = mysqli_query($conn, $query);
$expense = mysqli_fetch_assoc($result);

// Update expense record
if (isset($_POST['name']) && isset($_POST['amount']) && isset($_POST['date'])) {
    $name = $_POST['name'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $query = "UPDATE expenses SET name = '$name', amount = '$amount', date = '$date' WHERE id = '$expense_id'";
    mysqli_query($conn, $query);
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false));
}

// Close database connection
mysqli_close($conn);
?>