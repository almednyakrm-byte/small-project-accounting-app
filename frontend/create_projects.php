**create_projects.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
require_once 'header.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:px-12 xl:px-24">
    <div class="bg-white rounded-lg shadow-md p-4">
        <h2 class="text-slate-900 font-bold text-lg mb-4">Create New Project</h2>
        <form id="create-project-form">
            <div class="mb-4">
                <label for="project_name" class="text-slate-900 font-bold text-sm mb-2">Project Name:</label>
                <input type="text" id="project_name" name="project_name" class="w-full p-2 text-slate-900 border border-slate-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter project name">
            </div>
            <div class="mb-4">
                <label for="project_description" class="text-slate-900 font-bold text-sm mb-2">Project Description:</label>
                <textarea id="project_description" name="project_description" class="w-full p-2 text-slate-900 border border-slate-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter project description"></textarea>
            </div>
            <div class="mb-4">
                <label for="project_start_date" class="text-slate-900 font-bold text-sm mb-2">Project Start Date:</label>
                <input type="date" id="project_start_date" name="project_start_date" class="w-full p-2 text-slate-900 border border-slate-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="mb-4">
                <label for="project_end_date" class="text-slate-900 font-bold text-sm mb-2">Project End Date:</label>
                <input type="date" id="project_end_date" name="project_end_date" class="w-full p-2 text-slate-900 border border-slate-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">Create Project</button>
        </form>
    </div>
</div>

<?php
// Include footer
require_once 'footer.php';
?>

<script>
    $(document).ready(function() {
        $('#create-project-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/projects.php',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_projects.php';
                    } else {
                        alert('Error creating project');
                    }
                }
            });
        });
    });
</script>

**Note:** This code assumes you have jQuery and a backend PHP script (`projects.php`) to handle the form submission. You'll need to create those files and modify the code to fit your specific needs.