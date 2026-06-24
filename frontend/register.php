<!-- register.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen">
    <div class="flex justify-center items-center h-full">
        <div class="bg-white p-8 rounded-lg shadow-md w-1/2">
            <h1 class="text-3xl font-bold text-slate-900 mb-4">Register</h1>
            <form id="register-form">
                <div class="mb-4">
                    <label for="username" class="block text-slate-900 text-sm font-bold mb-2">Username</label>
                    <input type="text" id="username" name="username" class="block w-full p-2 pl-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Username" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-slate-900 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" class="block w-full p-2 pl-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Email" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-slate-900 text-sm font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="block w-full p-2 pl-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Password" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required>
                </div>
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Register</button>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('register-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const response = await fetch('../backend/auth.php?action=register', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                alert('Registration successful!');
                window.location.href = 'login.php';
            } else {
                alert(data.message);
            }
        });
    </script>
</body>
</html>


This code uses Tailwind CSS to create a premium-looking registration form. It includes validation rules and a pattern for the input fields, and submits the form via AJAX to the `auth.php` file in the backend. The response from the backend is then used to display a success or error message to the user.