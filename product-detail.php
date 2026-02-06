<?php
session_start();
require_once 'connection.php';

// Check if a product ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: shop.php');
    exit();
}

$productId = (int)$_GET['id'];
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? htmlspecialchars($_SESSION['user_fullname']) : '';

// Get the current cart count from the SESSION, not the database
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Fetch the specific product details from the database
$product = null;
$sql = "SELECT id, name, category, price, image_url FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
}

// If product not found, redirect to shop page
if ($product === null) {
    header('Location: shop.php');
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - ChicThreads</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Defer loading AlpineJS for better performance -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> .quantity-btn { transition: background-color 0.2s ease-in-out; } </style>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] }, colors: { primary: '#1a1a1a', accent: '#f7a440', lightgray: '#f5f5f5' } } } }
    </script>
</head>
<body x-data="{ cartCount: <?php echo $cart_count; ?> }" class="bg-lightgray text-primary font-sans antialiased">

    <!-- Header Section -->
    <header class="bg-white shadow-md sticky top-0 z-40">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
             <div>
                <a href="index.php" class="text-3xl font-bold text-primary tracking-wider">ChicThreads</a>
                 <?php if ($isLoggedIn): ?>
                    <p class="text-sm text-gray-500">Welcome, <?php echo $userName; ?>!</p>
                <?php endif; ?>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-gray-600 hover:text-accent">Home</a>
                <a href="shop.php" class="text-gray-600 hover:text-accent">Shop</a>
            </div>
            <div class="hidden md:flex items-center space-x-5">
                <!-- Corrected cart link to view_cart.php -->
                <a href="<?php echo $isLoggedIn ? 'view_cart.php' : 'login.php'; ?>" class="relative text-gray-600 hover:text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <template x-if="cartCount > 0">
                        <span x-text="cartCount" class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"></span>
                    </template>
                </a>
                 <?php if ($isLoggedIn): ?>
                    <a href="logout.php" class="bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- Main Product Details Section -->
    <main class="container mx-auto px-6 py-12">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
                <div> <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-auto object-cover rounded-lg shadow-md"> </div>
                <div class="flex flex-col justify-center">
                    <p class="text-gray-500 text-sm uppercase tracking-wide"><?php echo htmlspecialchars($product['category']); ?></p>
                    <h1 class="text-4xl md:text-5xl font-bold text-primary mt-2 mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="text-3xl font-light text-accent mb-6">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                    <div class="prose max-w-none text-gray-700 mb-8">
                        <p>This is a placeholder description for the <?php echo htmlspecialchars($product['name']); ?>. A real description would detail the fabric, fit, and styling options. It’s perfect for any occasion, combining comfort with contemporary fashion. Made from high-quality materials for durability and a great feel.</p>
                    </div>

                    <!-- Simplified Add to Cart Form -->
                    <?php if($isLoggedIn): ?>
                    <form action="add_to_cart.php" method="post">
                        <div x-data="{ quantity: 1 }">
                            <div class="flex items-center gap-4 mb-6">
                                <label for="quantity" class="font-semibold">Quantity:</label>
                                <div class="flex items-center border rounded-lg">
                                    <button type="button" @click="quantity = Math.max(1, quantity - 1)" class="quantity-btn p-2 text-gray-600 hover:bg-gray-100 rounded-l-lg">-</button>
                                    <!-- The name attribute is important for the form submission -->
                                    <input type="text" name="quantity" id="quantity" x-model="quantity" class="w-12 text-center border-none focus:ring-0" readonly>
                                    <button type="button" @click="quantity++" class="quantity-btn p-2 text-gray-600 hover:bg-gray-100 rounded-r-lg">+</button>
                                </div>
                            </div>
                            <!-- Hidden fields to pass all product data to add_to_cart.php -->
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                            <input type="hidden" name="image" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                            <button type="submit" class="w-full bg-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105"> Add to Cart </button>
                        </div>
                    </form>
                    <?php else: ?>
                        <!-- If not logged in, show a disabled button and a login link -->
                        <button type="button" class="w-full bg-gray-400 text-white font-bold py-3 px-6 rounded-lg cursor-not-allowed"> Add to Cart </button>
                        <p class="text-center text-sm text-gray-500 mt-2">You must be <a href="login.php" class="text-accent underline">logged in</a> to add items to the cart.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    <footer class="bg-primary text-white mt-16"> <div class="container mx-auto px-6 py-12 text-center"> <p>&copy; 2024 ChicThreads. All Rights Reserved.</p> </div> </footer>

</body>
</html>
