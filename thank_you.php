<?php
// You might start a session here if you wanted to display user-specific
// information, like an order number you've just generated. For this simple
// example, it's not strictly necessary.
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Order</title>
    <!-- Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="container mx-auto p-4">
        <div class="bg-white rounded-lg shadow-lg p-8 md:p-12 max-w-lg mx-auto text-center">
            <div class="mb-4">
                <svg class="mx-auto h-16 w-16 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Thank You!</h1>
            <p class="text-gray-600 mb-6">Your order has been placed successfully.</p>
            <p class="text-gray-500 text-sm mb-8">We've sent a confirmation email to you. You can check the status of your order in your account page.</p>
            
            <a href="index.php" class="w-full inline-block rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">
                Continue Shopping
            </a>
        </div>
    </div>

</body>
</html>
