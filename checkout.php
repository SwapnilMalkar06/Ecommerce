<?php
// Always start the session on pages that need access to session variables.
session_start();

// Redirect to the cart page if the cart is empty or doesn't exist.
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: view_cart.php');
    exit();
}

// Calculate the total once to use it in the summary.
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// A simple (and not secure) way to handle form submission for this example.
// In a real application, you would add validation, sanitation, and
// process the payment through a secure gateway.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Here you would process the payment, save the order to a database,
    // clear the cart, and then redirect to a "thank you" page.

    // For this example, we'll just clear the cart and redirect.
    unset($_SESSION['cart']);

    header('Location: thank_you.php'); // We'll create this simple page next.
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <!-- Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">

    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">

            <!-- Order Summary Section -->
            <div class="lg:col-span-1 order-last md:order-first">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-gray-800 border-b pb-4 mb-4">Order Summary</h2>
                    <?php foreach ($_SESSION['cart'] as $item) : ?>
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center">
                                <img src="<?php echo htmlspecialchars($item['image'] ?? 'https://placehold.co/50x50/e2e8f0/334155?text=Item'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="h-12 w-12 rounded-md object-cover mr-4">
                                <div>
                                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="text-sm text-gray-500">Qty: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-800">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <div class="border-t pt-4 mt-4">
                        <div class="flex justify-between text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                </div>
                 <div class="mt-6 flex justify-center text-center text-sm text-gray-500">
                    <p>
                        <a href="view_cart.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                            <span aria-hidden="true">&larr;</span>
                            Return to Cart
                        </a>
                    </p>
                </div>
            </div>

            <!-- Shipping & Payment Form Section -->
            <div class="md:col-span-1 lg:col-span-2">
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Shipping & Payment Details</h1>

                    <form action="checkout.php" method="POST">
                        <!-- Shipping Information -->
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">Shipping Address</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="first-name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" id="first-name" name="first-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                             <div>
                                <label for="last-name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" id="last-name" name="last-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                <input type="text" id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                             <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                <input type="text" id="city" name="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label for="zip" class="block text-sm font-medium text-gray-700">ZIP / Postal Code</label>
                                <input type="text" id="zip" name="zip" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <h2 class="text-lg font-semibold text-gray-700 mt-8 mb-4">Payment Details</h2>
                        <div class="grid grid-cols-1 gap-4">
                             <div>
                                <label for="card-number" class="block text-sm font-medium text-gray-700">Card Number</label>
                                <input type="text" id="card-number" name="card-number" placeholder="•••• •••• •••• ••••" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="expiration-date" class="block text-sm font-medium text-gray-700">Expiration (MM/YY)</label>
                                    <input type="text" id="expiration-date" name="expiration-date" placeholder="MM/YY" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label for="cvc" class="block text-sm font-medium text-gray-700">CVC</label>
                                    <input type="text" id="cvc" name="cvc" placeholder="•••" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" class="w-full flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">
                                Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
