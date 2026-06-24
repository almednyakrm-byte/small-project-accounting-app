**edit_accounts.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get account ID from URL
$id = $_GET['id'];

// Fetch existing account details via AJAX
$script = '<script>
    fetch("../backend/accounts.php?id=' . $id . '")
    .then(response => response.json())
    .then(data => {
        document.getElementById("account_name").value = data.account_name;
        document.getElementById("account_email").value = data.account_email;
        document.getElementById("account_phone").value = data.account_phone;
    })
    .catch(error => console.error("Error:", error));
</script>';

// Form submission handler
$submitHandler = '<script>
    const form = document.getElementById("edit-account-form");
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        fetch("../backend/accounts.php", {
            method: "PUT",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "list_' . $_SESSION['mod_slug'] . '.php";
            } else {
                console.error("Error:", data.error);
            }
        })
        .catch(error => console.error("Error:", error));
    });
</script>';

// Include header and footer
include 'header.php';
?>

<!-- Edit Account Form -->
<div class="max-w-md mx-auto p-4 bg-white rounded shadow-md">
    <h2 class="text-lg font-bold text-slate-900 mb-4">Edit Account</h2>
    <form id="edit-account-form" class="space-y-4">
        <div class="flex flex-col">
            <label for="account_name" class="text-sm font-bold text-slate-900 mb-2">Account Name:</label>
            <input type="text" id="account_name" name="account_name" class="px-4 py-2 text-sm text-slate-900 border border-slate-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
        </div>
        <div class="flex flex-col">
            <label for="account_email" class="text-sm font-bold text-slate-900 mb-2">Account Email:</label>
            <input type="email" id="account_email" name="account_email" class="px-4 py-2 text-sm text-slate-900 border border-slate-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indogo-500" required>
        </div>
        <div class="flex flex-col">
            <label for="account_phone" class="text-sm font-bold text-slate-900 mb-2">Account Phone:</label>
            <input type="tel" id="account_phone" name="account_phone" class="px-4 py-2 text-sm text-slate-900 border border-slate-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
        </div>
        <button type="submit" class="px-4 py-2 text-sm text-indigo-500 bg-indigo-500 hover:bg-indigo-700 rounded">Save Changes</button>
    </form>
</div>

<?php echo $script . $submitHandler; ?>

<?php include 'footer.php'; ?>


**header.php**

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body>
    <?php echo $content; ?>
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.js"></script>
</body>
</html>


**footer.php**

<?php
// Include backend script
include '../backend/script.php';
?>


**script.php**

<?php
// Include database connection
include '../backend/db.php';

// Update account details
if (isset($_POST['account_name']) && isset($_POST['account_email']) && isset($_POST['account_phone'])) {
    $id = $_POST['id'];
    $account_name = $_POST['account_name'];
    $account_email = $_POST['account_email'];
    $account_phone = $_POST['account_phone'];

    $query = "UPDATE accounts SET account_name = '$account_name', account_email = '$account_email', account_phone = '$account_phone' WHERE id = '$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false, 'error' => mysqli_error($conn)));
    }
}
?>