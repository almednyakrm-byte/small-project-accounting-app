<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تطبيق إدارة حسابات للمشاريع الصغيرة</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .glassmorphism-card {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="flex justify-between items-center p-4 bg-slate-900 text-white">
        <h1 class="text-3xl font-bold">تطبيق إدارة حسابات للمشاريع الصغيرة</h1>
        <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='logout.php'">تسجيل خروج</button>
    </div>
    <div class="flex justify-center items-center p-4">
        <div class="glassmorphism-card w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/5 p-4">
            <h2 class="text-2xl font-bold">مرحباً</h2>
            <p class="text-lg">إدارة حساباتك للمشاريع الصغيرة</p>
        </div>
    </div>
    <div class="flex justify-center items-center p-4">
        <div class="glassmorphism-card w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/5 p-4">
            <h2 class="text-2xl font-bold">إحصائيات</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">المشاريع</h3>
                    <p id="projects-count" class="text-lg"></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">الحسابات</h3>
                    <p id="accounts-count" class="text-lg"></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">المصروفات</h3>
                    <p id="expenses-count" class="text-lg"></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">الإيرادات</h3>
                    <p id="incomes-count" class="text-lg"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-center items-center p-4">
        <div class="glassmorphism-card w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/5 p-4">
            <h2 class="text-2xl font-bold">إدارة</h2>
            <div class="flex justify-between items-center gap-4">
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='projects.php'">المشاريع</button>
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='accounts.php'">الحسابات</button>
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='expenses.php'">المصروفات</button>
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='incomes.php'">الإيرادات</button>
            </div>
        </div>
    </div>

    <script>
        // Fetch stats dynamically via Javascript API calls from the backend files
        fetch('api/stats.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('projects-count').textContent = data.projects_count;
                document.getElementById('accounts-count').textContent = data.accounts_count;
                document.getElementById('expenses-count').textContent = data.expenses_count;
                document.getElementById('incomes-count').textContent = data.incomes_count;
            })
            .catch(error => console.error(error));
    </script>
</body>
</html>


This code uses Tailwind CSS for styling and includes a session check to redirect to the login page if the user is not authenticated. It also includes a dynamic stats grid that fetches data from the backend via an API call. The layout is designed to feel premium with a glassmorphism card layout.