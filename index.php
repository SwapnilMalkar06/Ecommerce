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

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChicThreads - Modern Fashion</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .group:hover .group-hover\:block {
            display: block;
        }
        .transition-all {
            transition: all 0.3s ease-in-out;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        primary: '#1a1a1a',
                        accent: '#f7a440',
                        lightgray: '#f5f5f5'
                    }
                }
            }
        }
    </script>
</head>
<body x-data="{ cartCount: <?php echo $cart_count; ?> }" class="bg-white text-primary font-sans antialiased">

    <!-- Header Section -->
    <header x-data="{ mobileMenuOpen: false }" class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo & Welcome Message -->
            <div>
                <a href="index.php" class="text-3xl font-bold text-primary tracking-wider">ChicThreads</a>
                <?php if ($isLoggedIn) : ?>
                    <p class="text-sm text-gray-500">Welcome, <?php echo $userName; ?>!</p>
                <?php endif; ?>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-gray-600 hover:text-accent transition-colors">Home</a>
                <a href="<?php echo $isLoggedIn ? 'shop.php' : 'login.php'; ?>" class="text-gray-600 hover:text-accent transition-colors">Shop</a>
                <a href="<?php echo $isLoggedIn ? 'new_arrivals.php' : 'login.php'; ?>" class="text-gray-600 hover:text-accent transition-colors">New Arrivals</a>
                <a href="about.php" class="text-gray-600 hover:text-accent transition-colors">About</a>
                <a href="contact.php" class="text-gray-600 hover:text-accent transition-colors">Contact</a>
            </div>

            <!-- Header Icons & Login/Logout Button -->
            <div class="hidden md:flex items-center space-x-5">
                <!-- Search Bar -->
                 <div class="relative">
                    <form action="shop.php" method="GET" class="flex items-center">
                        <input type="text" name="search" placeholder="Search..." class="w-40 px-3 py-1.5 text-sm border border-gray-200 rounded-l-md focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all">
                        <button type="submit" class="bg-primary text-white p-2 rounded-r-md hover:bg-accent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>
                <a href="<?php echo $isLoggedIn ? 'view_cart.php' : 'login.php'; ?>" class="relative text-gray-600 hover:text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <template x-if="cartCount > 0">
                        <span x-text="cartCount" class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"></span>
                    </template>
                </a>

                <?php if ($isLoggedIn) : ?>
                    <!-- Show Logout Button if logged in -->
                    <a href="logout.php" class="bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105">
                        Logout
                    </a>
                <?php else : ?>
                    <!-- Show Login Button if not logged in -->
                    <a href="login.php" class="bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105">
                        Login
                    </a>
                <?php endif; ?>

            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-accent focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden bg-white" x-transition>
            <a href="index.php" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">Home</a>
            <a href="<?php echo $isLoggedIn ? 'shop.php' : 'login.php'; ?>" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">Shop</a>
            <a href="<?php echo $isLoggedIn ? 'new_arrivals.php' : 'login.php'; ?>" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">New Arrivals</a>
            <a href="about.php" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">About</a>
            <a href="contact.php" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">Contact</a>
            <div class="border-t my-2"></div>
            <div class="px-6 py-2">
                <?php if ($isLoggedIn) : ?>
                    <a href="logout.php" class="block w-full text-center bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all duration-300">
                        Logout
                    </a>
                <?php else : ?>
                    <a href="login.php" class="block w-full text-center bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-accent transition-all duration-300">
                        Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-lightgray">
        <div class="container mx-auto px-6 py-20 text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-primary mb-4 leading-tight">Effortless Style, <span class="text-accent">Delivered</span></h1>
            <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">Discover curated collections of modern fashion that
                fit your lifestyle. Quality and comfort, guaranteed.</p>
            <a href="<?php echo $isLoggedIn ? 'shop.php' : 'login.php'; ?>" class="bg-primary text-white font-semibold py-3 px-8 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105">
                Shop Now
            </a>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="container mx-auto px-6 py-16">
         <h2 class="text-3xl font-bold text-center text-primary mb-10">Shop by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            
            <?php 
                $categories = [
                    'Dresses' => 'https://images.unsplash.com/photo-1595535373182-fcb2f246e38b?q=80&w=800&auto=format&fit=crop',
                    'Tops' => 'https://images.unsplash.com/photo-1581655353564-df123a50493f?q=80&w=800&auto=format&fit=crop',
                    'Jeans' => 'https://images.unsplash.com/photo-1602293589914-9FF0554ba739?q=80&w=800&auto=format&fit=crop',
                    'Outerwear' => 'https://images.unsplash.com/photo-1591047139829-d919b5ca2373?q=80&w=800&auto=format&fit=crop',
                    'Shoes' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ab?q=80&w=800&auto=format&fit=crop',
                    'Accessories' => 'https://images.unsplash.com/photo-1588404533928-e43c8473859b?q=80&w=800&auto=format&fit=crop',
                    'Bags' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?q=80&w=800&auto=format&fit=crop',
                    'Watches' => 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?q=80&w=800&auto=format&fit=crop',
                    'Skirts' => 'https://images.unsplash.com/photo-1589467262457-194203673191?q=80&w=800&auto=format&fit=crop',
                    'Kids' => 'https://images.unsplash.com/photo-1604928141068-a2d04573a2d2?q=80&w=800&auto=format&fit=crop',
                    'Electronics' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?q=80&w=800&auto=format&fit=crop',
                    'Unisex' => 'https://images.unsplash.com/photo-1512314889357-e157c22f938d?q=80&w=800&auto=format&fit=crop'
                ];

                foreach ($categories as $name => $image) :
            ?>
            <a href="shop.php?category=<?php echo urlencode($name); ?>" class="relative rounded-lg overflow-hidden group aspect-w-1 aspect-h-1">
                <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <h3 class="text-white text-xl font-bold"><?php echo $name; ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="bg-accent text-white">
        <div class="container mx-auto px-6 py-12">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-6 md:mb-0 text-center md:text-left">
                    <h3 class="text-3xl font-bold">Join Our Newsletter</h3>
                    <p class="text-gray-200 mt-2">Get 15% off your first order and stay up-to-date with our latest
                        arrivals.</p>
                </div>
                <div class="md:w-1/2">
                    <form class="flex flex-col sm:flex-row gap-3">
                        <input type="email" placeholder="Enter your email address" class="w-full px-4 py-3 rounded-lg text-primary focus:outline-none focus:ring-2 focus:ring-white" required>
                        <button type="submit" class="bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-opacity-80 transition-colors">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About Section -->
                <div>
                    <h4 class="text-xl font-bold mb-4">ChicThreads</h4>
                    <p class="text-gray-400">Your destination for modern, high-quality fashion that makes you feel
                        confident and stylish.</p>
                </div>
                <!-- Quick Links -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="<?php echo $isLoggedIn ? 'shop.php' : 'login.php'; ?>" class="text-gray-400 hover:text-white transition-colors">Shop</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">My Account</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Track Order</a></li>
                    </ul>
                </div>
                <!-- Help -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Help & Info</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Shipping</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Returns</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>
                <!-- Social Media -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.012-3.808-.06c-1.064-.049-1.791-.218-2.427.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791-.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.06-1.004.048-1.625.211-2.126.41-1.054.423-1.763 1.132-2.186 2.186-.2.499-.363 1.121-.41 2.126-.05 1.023-.06 1.351-.06 3.807v.468c0 2.456.011 2.784.06 3.807.048 1.004.211 1.625.41 2.126.423 1.054 1.132 1.763 2.186 2.186.499.2.921.363 2.126.41 1.023.05 1.351.06 3.807.06h.468c2.456 0 2.784-.011 3.807-.06 1.004-.048 1.625-.211 2.126-.41 1.054-.423 1.763-1.132 2.186-2.186.2-.499-.363-1.121.41-2.126.05-1.023.06-1.351.06-3.807v-.468c0-2.456-.011-2.784-.06-3.807-.048-1.004-.211-1.625-.41-2.126-.423-1.054-1.132-1.763-2.186-2.186-.499-.2-.921-.363-2.126-.41-1.023-.05-1.351-.06-3.807-.06zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 11-2.4 0 1.2 1.2 0 012.4 0z" clip-rule="evenodd" /></svg></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; 2024 ChicThreads. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>

