**edit_incomes.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the income ID from the URL
$id = $_GET['id'];

// Fetch the existing record details via GET
$url = '../backend/incomes.php?id=' . $id;
$response = file_get_contents($url);
$data = json_decode($response, true);

// Set the form fields
$income_date = $data['income_date'];
$income_amount = $data['income_amount'];
$income_description = $data['income_description'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Income</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Edit Income</h2>
        <form id="edit-income-form">
            <div class="mb-4">
                <label for="income_date" class="block text-sm font-medium text-slate-900">Date:</label>
                <input type="date" id="income_date" name="income_date" class="block w-full p-2 pl-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" value="<?= $income_date ?>">
            </div>
            <div class="mb-4">
                <label for="income_amount" class="block text-sm font-medium text-slate-900">Amount:</label>
                <input type="number" id="income_amount" name="income_amount" class="block w-full p-2 pl-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" value="<?= $income_amount ?>">
            </div>
            <div class="mb-4">
                <label for="income_description" class="block text-sm font-medium text-slate-900">Description:</label>
                <textarea id="income_description" name="income_description" class="block w-full p-2 pl-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" rows="4"><?= $income_description ?></textarea>
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Update Income</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-income-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/incomes.php',
                    data: formData,
                    success: function(data) {
                        if (data.status === 'success') {
                            window.location.href = 'list_incomes.php';
                        } else {
                            alert(data.message);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>


**incomes.php (backend)**

<?php
// Check if the income ID is set
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch the existing record details from the database
    $query = "SELECT * FROM incomes WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    echo json_encode($data);
} else {
    // Handle invalid request
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request'));
}
?>


**Note:** This code assumes you have a `mysqli` connection established in your `incomes.php` file. You should replace the `mysqli` functions with your own database interaction code. Additionally, this code does not include any validation or sanitization of user input, which you should add to prevent security vulnerabilities.