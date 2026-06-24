**create_expenses.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include 'header.php';
?>

<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-slate-900 mb-4">Add New Expense</h1>
    <form id="expense-form" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="date">Date:</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="date" type="date" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Category:</label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category" required>
                <option value="">Select Category</option>
                <option value="Rent">Rent</option>
                <option value="Utilities">Utilities</option>
                <option value="Food">Food</option>
                <option value="Transportation">Transportation</option>
                <option value="Miscellaneous">Miscellaneous</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="amount">Amount:</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="amount" type="number" step="0.01" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description:</label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" required></textarea>
        </div>
        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Add Expense</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#expense-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/expenses.php',
                data: formData,
                success: function(response) {
                    if (response == 'success') {
                        window.location.href = 'list_expenses.php';
                    } else {
                        alert('Error adding expense');
                    }
                }
            });
        });
    });
</script>

<?php
// Include footer
include 'footer.php';
?>


**expenses.php (backend)**

<?php
// Include database connection
include 'db.php';

// Check if form data is submitted
if (isset($_POST['date']) && isset($_POST['category']) && isset($_POST['amount']) && isset($_POST['description'])) {
    // Insert data into database
    $date = $_POST['date'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $query = "INSERT INTO expenses (date, category, amount, description) VALUES ('$date', '$category', '$amount', '$description')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo 'success';
    } else {
        echo 'Error adding expense';
    }
}
?>


**Note:** This code assumes that you have a database connection established in `db.php` and a table named `expenses` with columns `date`, `category`, `amount`, and `description`. You should replace the placeholder values with your actual database credentials and table structure. Additionally, this code uses the `mysqli` extension for database interactions, which is deprecated in PHP 7.0 and removed in PHP 8.0. You should consider using the `PDO` extension or `mysqli` with prepared statements for better security and performance.