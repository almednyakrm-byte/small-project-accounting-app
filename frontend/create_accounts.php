**create_accounts.php**

<?php
// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include_once 'header.php';
?>

<div class="container mx-auto p-4 pt-6">
    <div class="bg-white rounded shadow-md p-4">
        <h2 class="text-slate-900 font-bold text-lg mb-4">Create New Account</h2>
        <form id="create-account-form">
            <div class="mb-4">
                <label for="account_name" class="text-slate-900 font-bold">Account Name:</label>
                <input type="text" id="account_name" name="account_name" class="bg-gray-100 border border-gray-300 text-slate-900 text-sm rounded px-2 py-1 w-full" required>
            </div>
            <div class="mb-4">
                <label for="account_type" class="text-slate-900 font-bold">Account Type:</label>
                <select id="account_type" name="account_type" class="bg-gray-100 border border-gray-300 text-slate-900 text-sm rounded px-2 py-1 w-full" required>
                    <option value="">Select Account Type</option>
                    <option value="Checking">Checking</option>
                    <option value="Savings">Savings</option>
                    <option value="Credit">Credit</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="account_balance" class="text-slate-900 font-bold">Account Balance:</label>
                <input type="number" id="account_balance" name="account_balance" class="bg-gray-100 border border-gray-300 text-slate-900 text-sm rounded px-2 py-1 w-full" required>
            </div>
            <div class="mb-4">
                <label for="account_status" class="text-slate-900 font-bold">Account Status:</label>
                <select id="account_status" name="account_status" class="bg-gray-100 border border-gray-300 text-slate-900 text-sm rounded px-2 py-1 w-full" required>
                    <option value="">Select Account Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Create Account</button>
        </form>
    </div>
</div>

<?php
// Include footer
include_once 'footer.php';
?>

<script>
    $(document).ready(function() {
        $('#create-account-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/accounts.php',
                data: formData,
                success: function(response) {
                    if (response == 'success') {
                        window.location.href = 'list_accounts.php';
                    } else {
                        alert('Error creating account');
                    }
                }
            });
        });
    });
</script>


**accounts.php (backend)**

<?php
// Include database connection
include_once 'db.php';

// Check if form data is submitted
if (isset($_POST['account_name']) && isset($_POST['account_type']) && isset($_POST['account_balance']) && isset($_POST['account_status'])) {
    // Prepare SQL query
    $sql = "INSERT INTO accounts (account_name, account_type, account_balance, account_status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $_POST['account_name'], $_POST['account_type'], $_POST['account_balance'], $_POST['account_status']);
    // Execute query
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Error creating account';
    }
    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>