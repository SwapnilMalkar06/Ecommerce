<?php
// Initialize a variable to hold response messages
$response = '';

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Include the database connection configuration
    // Make sure 'connection.php' is in the same directory
    require_once 'connection.php';

    // --- Data Collection & Sanitization ---
    $fullname = isset($_POST['fullname']) ? htmlspecialchars(trim($_POST['fullname'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // --- Basic Server-Side Validation ---
    $errors = [];

    if (empty($fullname)) {
        $errors[] = "Full Name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // --- Process Data if no validation errors ---
    if (empty($errors)) {
        // Check if email already exists
        $stmt_check = $conn->prepare("SELECT id FROM user_data WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $errors[] = "This email address is already registered.";
        }
        $stmt_check->close();

        // If still no errors, proceed with insertion
        if(empty($errors)) {
            // Prepare an INSERT statement to prevent SQL injection
            $stmt_insert = $conn->prepare("INSERT INTO user_data (fullname, email, password) VALUES (?, ?, ?)");
            
            if ($stmt_insert) {
                // Bind the plain text password directly
                $stmt_insert->bind_param("sss", $fullname, $email, $password);

                if ($stmt_insert->execute()) {
                    // Success message
                    $response = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert"><strong>Success!</strong> Your account has been created.</div>';
                } else {
                    $errors[] = "An unexpected error occurred. Please try again later.";
                }
                $stmt_insert->close();
            } else {
                $errors[] = "Database error: Could not prepare the statement.";
            }
        }
    }

    // --- Format and Display Errors ---
    if (!empty($errors)) {
        $response = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">';
        $response .= '<strong>Error!</strong> Please fix the following issues:<ul>';
        foreach ($errors as $error) {
            $response .= '<li class="list-disc ml-5">' . $error . '</li>';
        }
        $response .= '</ul></div>';
    }

    // Close the database connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Chic Threads</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1a1a1a',
                        accent: '#f7a440',
                        lightgray: '#f5f5f5',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-lightgray text-primary font-sans antialiased">

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="index.php" class="text-2xl font-bold text-primary tracking-wider">ChicThreads</a>
                <a href="index.php" class="text-gray-600 hover:text-accent transition-colors duration-300">Back to Shop</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex items-center justify-center min-h-screen py-12 px-6" style="background-image: url('https://placehold.co/1920x1080/f5f5f5/cccccc?text='); background-size: cover;">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
            <h1 class="text-3xl font-bold text-center text-primary mb-2">Create Your Account</h1>
            <p class="text-center text-gray-500 mb-6">Join the ChicThreads family!</p>
            
            <!-- Response Message Area -->
            <div class="mb-4">
                <?php echo $response; ?>
            </div>

            <!-- Signup Form -->
            <!-- The action attribute is empty, so it submits to the same file -->
            <form action="signup-combined.php" method="POST">
                <div class="mb-4">
                    <label for="fullname" class="block text-gray-700 font-semibold mb-2">Full Name</label>
                    <input type="text" id="fullname" name="fullname" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" placeholder="John Doe" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" placeholder="you@example.com" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" placeholder="••••••••" required>
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="block text-gray-700 font-semibold mb-2">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" placeholder="••••••••" required>
                </div>
                <button type="submit" class="w-full bg-primary text-white font-semibold py-3 px-8 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105">
                    Sign Up
                </button>
            </form>
            
            <p class="text-center text-gray-500 mt-6">
                Already have an account? <a href="login.php" class="text-accent font-semibold hover:underline">Log in</a>.
            </p>
        </div>
    </main>

</body>
</html>

