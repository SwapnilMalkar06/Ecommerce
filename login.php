<?php
// Start the session at the very beginning
session_start();

// If the user is already logged in, redirect them to the home page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize a variable to hold response messages
$response = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once 'connection.php';

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    $errors = [];

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        // Prepare a statement to select the user by email
        $stmt = $conn->prepare("SELECT id, fullname, password FROM user_data WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            // Check if a user with that email exists
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $fullname, $db_password);
                $stmt->fetch();

                // Verify the password (plain text comparison as requested)
                if ($password === $db_password) {
                    // Password is correct, so start a new session
                    session_regenerate_id(); // Mitigates session fixation
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_fullname'] = $fullname;

                    // Redirect to the home page
                    header("Location: index.php");
                    exit(); // Important to stop script execution after redirect
                } else {
                    $errors[] = "Invalid email or password.";
                }
            } else {
                $errors[] = "Invalid email or password.";
            }
            $stmt->close();
        } else {
             $errors[] = "Database error: Could not prepare the statement.";
        }
    }
    
    // Format and display errors
    if (!empty($errors)) {
        $response = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">';
        $response .= '<strong>Error!</strong> ' . $errors[0]; // Display first error
        $response .= '</div>';
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chic Threads</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] }, colors: { primary: '#1a1a1a', accent: '#f7a440', lightgray: '#f5f5f5' } } }
        }
    </script>
</head>
<body class="bg-lightgray text-primary font-sans">

    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-primary tracking-wider">ChicThreads</a>
        </div>
    </header>

    <main class="flex items-center justify-center min-h-screen py-12 px-6" style="margin-top: -68px;">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
            <h1 class="text-3xl font-bold text-center text-primary mb-2">Welcome Back!</h1>
            <p class="text-center text-gray-500 mb-6">Log in to continue shopping.</p>
            
            <div class="mb-4">
                <?php echo $response; ?>
            </div>

            <form action="login.php" method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" placeholder="you@example.com" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" placeholder="••••••••" required>
                </div>
                <button type="submit" class="w-full bg-primary text-white font-semibold py-3 px-8 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105">
                    Log In
                </button>
            </form>
            
            <p class="text-center text-gray-500 mt-6">
                Don't have an account? <a href="signup-combined.php" class="text-accent font-semibold hover:underline">Sign up here</a>.
            </p>
        </div>
    </main>

</body>
</html>
