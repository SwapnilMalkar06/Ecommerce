<?php
// Start the session at the very beginning
session_start();
require_once 'connection.php'; // Include the database connection

// Check if the user is logged in and set a flag
$isLoggedIn = isset($_SESSION['user_id']);

// If logged in, get the user's name
$userName = $isLoggedIn ? htmlspecialchars($_SESSION['user_fullname']) : '';

// Get the current cart count from the SESSION for the header icon
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Fetch the 8 most recent products
$products = [];
$sql = "SELECT id, name, category, price, image_url FROM products ORDER BY created_at DESC LIMIT 8";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    $noProductsMessage = "No new products found at the moment. Please check back soon!";
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Arrivals - ChicThreads</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .group:hover .group-hover\:block { display: block; }
        .transition-all { transition: all 0.3s ease-in-out; }
    </style>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] }, colors: { primary: '#1a1a1a', accent: '#f7a440', lightgray: '#f5f5f5' } } }
        }
    </script>
</head>
<body x-data="{ cartCount: <?php echo $cart_count; ?> }" class="bg-lightgray text-primary font-sans antialiased">

    <!-- Header Section (Consistent with other pages) -->
    <header x-data="{ mobileMenuOpen: false }" class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo & Welcome Message -->
            <div>
                <a href="index.php" class="text-3xl font-bold text-primary tracking-wider">ChicThreads</a>
                <?php if ($isLoggedIn): ?>
                    <p class="text-sm text-gray-500">Welcome, <?php echo $userName; ?>!</p>
                <?php endif; ?>
            </div>
            
            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-gray-600 hover:text-accent transition-colors">Home</a>
                <a href="shop.php" class="text-gray-600 hover:text-accent transition-colors">Shop</a>
                <a href="new_arrivals.php" class="text-accent font-semibold transition-colors">New Arrivals</a>
                <a href="about.php" class="text-gray-600 hover:text-accent transition-colors">About</a>
                <a href="contact.php" class="text-gray-600 hover:text-accent transition-colors">Contact</a>
            </div>

            <!-- Header Icons & Login/Logout Button -->
            <div class="hidden md:flex items-center space-x-5">
                <a href="<?php echo $isLoggedIn ? 'view_cart.php' : 'login.php'; ?>" class="relative text-gray-600 hover:text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <template x-if="cartCount > 0">
                        <span x-text="cartCount" class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"></span>
                    </template>
                </a>
                
                <?php if ($isLoggedIn): ?>
                    <a href="logout.php" class="bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105">Login</a>
                <?php endif; ?>
            </div>
            
            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-accent focus:outline-none"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg></button>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden bg-white" x-transition>
            <a href="index.php" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">Home</a>
            <a href="shop.php" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">Shop</a>
            <a href="new_arrivals.php" class="block py-2 px-6 text-sm text-accent font-semibold">New Arrivals</a>
            <a href="about.php" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">About</a>
            <a href="contact.php" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">Contact</a>
            <div class="border-t my-2"></div>
            <div class="px-6 py-2">
                 <?php if ($isLoggedIn): ?>
                    <a href="logout.php" class="block w-full text-center bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent">Logout</a>
                 <?php else: ?>
                    <a href="login.php" class="block w-full text-center bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent">Login</a>
                 <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-primary">New Arrivals</h1>
            <p class="text-lg text-gray-600 mt-2">Check out the latest additions to our collection.</p>
        </div>

        <!-- Products Grid -->
        <section>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                    <!-- Product Card -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                        <div class="relative">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-auto object-cover" style="height: 500px;">
                            </a>
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                                <?php if ($isLoggedIn) : ?>
                                    <form action="add_to_cart.php" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                        <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                        <input type="hidden" name="image" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                                        <button type="submit" class="text-white bg-primary py-2 px-4 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">Add to Cart</button>
                                    </form>
                                <?php else : ?>
                                    <a href="login.php" class="text-white bg-primary py-2 px-4 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">Add to Cart</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="block">
                                <h3 class="text-lg font-semibold text-primary hover:text-accent transition-colors"><?php echo htmlspecialchars($product['name']); ?></h3>
                            </a>
                            <p class="text-gray-500 mt-1"><?php echo htmlspecialchars($product['category']); ?></p>
                            <p class="text-primary font-bold mt-2">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-600 col-span-full text-center"><?php echo $noProductsMessage; ?></p>
                <?php endif; ?>

            </div>
        </section>
    </main>

    <!-- Footer (Same as other pages for consistency) -->
    <footer class="bg-primary text-white mt-16">
        <div class="container mx-auto px-6 py-12">
             <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; 2024 ChicThreads. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
