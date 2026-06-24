**create_incomes.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include 'header.php';
include 'nav.php';

// Include form script
include 'form_script.php';

?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-12 2xl:p-12">
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 lg:p-8 xl:p-8 2xl:p-8">
        <h2 class="text-slate-900 font-bold text-lg mb-4">Create New Income</h2>
        <form id="create-income-form">
            <div class="mb-4">
                <label for="date" class="text-slate-900 font-bold">Date:</label>
                <input type="date" id="date" name="date" class="bg-gray-100 border border-gray-200 rounded-lg py-2 px-4 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="mb-4">
                <label for="amount" class="text-slate-900 font-bold">Amount:</label>
                <input type="number" id="amount" name="amount" class="bg-gray-100 border border-gray-200 rounded-lg py-2 px-4 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="mb-4">
                <label for="description" class="text-slate-900 font-bold">Description:</label>
                <textarea id="description" name="description" class="bg-gray-100 border border-gray-200 rounded-lg py-2 px-4 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">Create Income</button>
        </form>
    </div>
</div>

<?php
// Include footer
include 'footer.php';
?>


**form_script.php**

<script>
    $(document).ready(function() {
        $('#create-income-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/incomes.php',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_incomes.php';
                    } else {
                        alert('Error creating income: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error creating income: ' + error);
                }
            });
        });
    });
</script>


**incomes.php (backend)**

<?php
// Include database connection
include 'db.php';

// Check if form data is submitted
if (isset($_POST['date']) && isset($_POST['amount']) && isset($_POST['description'])) {
    // Prepare SQL query
    $sql = "INSERT INTO incomes (date, amount, description) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['date'], $_POST['amount'], $_POST['description']]);

    // Check if query was successful
    if ($stmt->rowCount() > 0) {
        echo 'success';
    } else {
        echo 'Error creating income: ' . $pdo->errorInfo()[2];
    }
}
?>