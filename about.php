<?php
// Start the session at the very beginning
session_start();
require_once 'connection.php'; // Include the database connection for consistency

// Check if the user is logged in and set a flag
$isLoggedIn = isset($_SESSION['user_id']);

// If logged in, get the user's name
$userName = $isLoggedIn ? htmlspecialchars($_SESSION['user_fullname']) : '';

// Get the current cart count from the SESSION for the header icon
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// No database queries are needed for this page, but we close the connection if it was opened
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ChicThreads</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] }, colors: { primary: '#1a1a1a', accent: '#f7a440', lightgray: '#f5f5f5' } } }
        }
    </script>
</head>
<body x-data="{ cartCount: <?php echo $cart_count; ?> }" class="bg-white text-primary font-sans antialiased">

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
                <a href="new_arrivals.php" class="text-gray-600 hover:text-accent transition-colors">New Arrivals</a>
                <a href="about.php" class="text-accent font-semibold transition-colors">About</a>
                <a href="#" class="text-gray-600 hover:text-accent transition-colors">Contact</a>
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
                    <a href="logout.php" class="bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all">Login</a>
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
            <a href="new_arrivals.php" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">New Arrivals</a>
            <a href="about.php" class="block py-2 px-6 text-sm text-accent font-semibold">About</a>
            <a href="#" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">Contact</a>
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
    <main>
        <!-- Hero Section -->
        <section class="bg-lightgray py-20 text-center">
            <div class="container mx-auto px-6">
                <h1 class="text-4xl md:text-5xl font-bold text-primary">About ChicThreads</h1>
                <p class="text-lg text-gray-600 mt-4 max-w-3xl mx-auto">We believe that fashion is more than just clothing — it's a form of self-expression. Discover the story and passion behind every thread.</p>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="container mx-auto px-6 py-16">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <img src="https://placehold.co/600x400/f7a440/1a1a1a?text=Our+Journey" alt="Our Story" class="rounded-lg shadow-xl w-full">
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-primary mb-4">Our Story</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        ChicThreads was born from a simple idea: to make modern, high-quality fashion accessible to everyone. What started as a small passion project in a humble studio has grown into a destination for style-conscious individuals who value quality, comfort, and timeless design.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Every piece in our collection is thoughtfully curated and designed with you in mind. We're inspired by the world around us, from the clean lines of modern architecture to the vibrant energy of city life.
                    </p>
                </div>
            </div>
        </section>

        <!-- Our Mission Section -->
        <section class="bg-lightgray py-16">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-primary mb-4">Our Mission & Values</h2>
                <p class="text-gray-700 leading-relaxed max-w-3xl mx-auto mb-10">
                    Our mission is to empower you to feel confident and stylish every day. We are committed to ethical sourcing, sustainable practices, and creating pieces that you'll love for years to come.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-2">Quality Craftsmanship</h3>
                        <p class="text-gray-600">We obsess over the details, from the choice of fabric to the final stitch, ensuring every garment meets our high standards.</p>
                    </div>
                    <div class="bg-white p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-2">Timeless Style</h3>
                        <p class="text-gray-600">We focus on creating versatile, timeless pieces that transcend fleeting trends and become staples in your wardrobe.</p>
                    </div>
                    <div class="bg-white p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-2">Customer First</h3>
                        <p class="text-gray-600">Your satisfaction is our top priority. We're dedicated to providing an exceptional shopping experience from start to finish.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="container mx-auto px-6 py-16 text-center">
            <h2 class="text-3xl font-bold text-primary mb-4">Join the ChicThreads Family</h2>
            <p class="text-gray-700 max-w-2xl mx-auto mb-8">Ready to elevate your style? Explore our curated collections and find your new favorite piece today.</p>
            <a href="shop.php" class="bg-primary text-white font-semibold py-3 px-8 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105">
                Shop Our Collection
            </a>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-primary text-white">
        <div class="container mx-auto px-6 py-12 text-center text-gray-400">
             <div class="border-t border-gray-700 pt-6">
                <p>&copy; 2024 ChicThreads. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
