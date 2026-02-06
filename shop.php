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

// --- FILTERING & SEARCH LOGIC ---
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
$selectedAudience = isset($_GET['audience']) ? $_GET['audience'] : null;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 500;
$searchTerm = isset($_GET['search']) ? $_GET['search'] : null;

// Fetch unique categories for the filter list
$categories = [];
$categorySql = "SELECT DISTINCT category FROM products ORDER BY category ASC";
$categoryResult = $conn->query($categorySql);
if ($categoryResult && $categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Fetch unique audiences for the filter list
$audiences = [];
$audienceSql = "SELECT DISTINCT audience FROM products WHERE audience IS NOT NULL AND audience != '' ORDER BY audience ASC";
$audienceResult = $conn->query($audienceSql);
if ($audienceResult && $audienceResult->num_rows > 0) {
    while ($row = $audienceResult->fetch_assoc()) {
        $audiences[] = $row['audience'];
    }
}


// Define the structure for sub-category grouping in the filter
$categoryGroups = [
    'Apparel' => ['Dresses', 'Tops', 'Jeans', 'Outerwear', 'Skirts'],
    'Accessories' => ['Accessories', 'Bags', 'Watches', 'Shoes'],
    'Electronics' => ['Electronics']
];


// Build the product query based on filters
$products = [];
// Base SQL query
$sql = "SELECT id, name, category, price, image_url, audience FROM products WHERE price <= ?";
$params = ['i', $maxPrice]; // 'i' for integer type for price

// Add category filter if one is selected
if ($selectedCategory) {
    $sql .= " AND category = ?";
    $params[0] .= 's'; // add 's' for string type for category
    $params[] = $selectedCategory;
}

// Add audience filter if one is selected
if ($selectedAudience) {
    $sql .= " AND audience = ?";
    $params[0] .= 's';
    $params[] = $selectedAudience;
}

// Add search term filter if one is provided
if ($searchTerm) {
    $sql .= " AND name LIKE ?";
    $params[0] .= 's'; // add 's' for string type for search term
    $params[] = '%' . $searchTerm . '%';
}


$sql .= " ORDER BY created_at DESC";

// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Dynamically bind parameters using the splat operator (...)
    $stmt->bind_param(...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    } else {
        $noProductsMessage = "No products found matching your criteria.";
    }
    $stmt->close();
} else {
    // Handle potential errors with the SQL query preparation
    $noProductsMessage = "Error preparing the product query.";
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - ChicThreads</title>
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
        [x-cloak] { display: none !important; }
    </style>
    <script>
        tailwind.config = {
            theme: { 
                extend: { 
                    fontFamily: { sans: ['Inter', 'sans-serif'] }, 
                    colors: { primary: '#1a1a1a', accent: '#f7a440', lightgray: '#f5f5f5' } 
                } 
            }
        }
    </script>
</head>
<body x-data="{ cartCount: <?php echo $cart_count; ?> }" class="bg-lightgray text-primary font-sans antialiased">

    <!-- Header Section (Same as index.php for consistency) -->
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
                <a href="shop.php" class="text-accent font-semibold transition-colors">Shop</a>
                <a href="<?php echo $isLoggedIn ? 'new_arrivals.php' : 'login.php'; ?>" class="text-gray-600 hover:text-accent transition-colors">New Arrivals</a>
                <a href="about.php" class="text-gray-600 hover:text-accent transition-colors">About</a>
                <a href="contact.php" class="text-gray-600 hover:text-accent transition-colors">Contact</a>
            </div>

            <!-- Header Icons & Login/Logout Button -->
            <div class="hidden md:flex items-center space-x-5">
                 <!-- Search Bar -->
                 <div class="relative">
                    <form action="shop.php" method="GET" class="flex items-center">
                        <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>" class="w-40 px-3 py-1.5 text-sm border border-gray-200 rounded-l-md focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all">
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
            <a href="shop.php" class="block py-2 px-6 text-sm text-accent font-semibold">Shop</a>
            <a href="<?php echo $isLoggedIn ? 'new_arrivals.php' : 'login.php'; ?>" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">New Arrivals</a>
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
            <h1 class="text-4xl md:text-5xl font-bold text-primary">Our Collection</h1>
            <p class="text-lg text-gray-600 mt-2">Browse our hand-picked selection of the latest trends.</p>
        </div>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Filters Sidebar -->
            <aside class="w-full md:w-1/4 lg:w-1/5">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2">Filters</h3>
                    
                    <!-- Audience Filter -->
                    <div class="mb-6">
                        <h4 class="font-semibold mb-3">Shop For</h4>
                        <ul class="space-y-2 text-gray-600">
                             <li><a href="shop.php?category=<?php echo urlencode($selectedCategory); ?>&max_price=<?php echo $maxPrice; ?>" class="block py-1 <?php echo (!$selectedAudience) ? 'text-accent font-bold' : 'hover:text-accent'; ?>">All</a></li>
                            <?php foreach ($audiences as $audience): ?>
                            <li>
                                <a href="shop.php?audience=<?php echo urlencode($audience); ?>&category=<?php echo urlencode($selectedCategory); ?>&max_price=<?php echo $maxPrice; ?>" class="block <?php echo ($selectedAudience === $audience) ? 'text-accent font-bold' : 'hover:text-accent'; ?>">
                                    <?php echo htmlspecialchars($audience); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-6 border-t pt-4">
                        <h4 class="font-semibold mb-3">Category</h4>
                        <ul class="space-y-2 text-gray-600">
                            <li><a href="shop.php?audience=<?php echo urlencode($selectedAudience); ?>&max_price=<?php echo $maxPrice; ?>" class="block py-1 <?php echo (!$selectedCategory) ? 'text-accent font-bold' : 'hover:text-accent'; ?>">All</a></li>
                            
                            <?php foreach ($categoryGroups as $groupName => $groupCategories): ?>
                                <?php 
                                    // Check if there are any available categories for this group to display
                                    $availableCatsInGroup = array_intersect($groupCategories, $categories);
                                    if (empty($availableCatsInGroup)) continue;
                                ?>
                                <div x-data="{ open: true }" class="py-2">
                                    <h5 @click="open = !open" class="font-semibold text-primary cursor-pointer flex justify-between items-center">
                                        <?php echo $groupName; ?>
                                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </h5>
                                    <ul x-show="open" x-cloak class="pl-2 mt-2 space-y-2 border-l-2 border-lightgray" x-transition>
                                        <?php foreach ($availableCatsInGroup as $category): ?>
                                            <li>
                                                <a href="shop.php?audience=<?php echo urlencode($selectedAudience); ?>&category=<?php echo urlencode($category); ?>&max_price=<?php echo $maxPrice; ?>" class="block <?php echo ($selectedCategory === $category) ? 'text-accent font-bold' : 'hover:text-accent'; ?>">
                                                    <?php echo htmlspecialchars($category); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Price Range Filter -->
                    <div x-data="{ currentMaxPrice: <?php echo $maxPrice; ?> }">
                        <h4 class="font-semibold mb-3 border-t pt-4">Price Range</h4>
                        <form action="shop.php" method="GET" id="price-filter-form">
                            <!-- Hidden fields to carry over other filters -->
                            <?php if ($selectedCategory): ?>
                                <input type="hidden" name="category" value="<?php echo htmlspecialchars($selectedCategory); ?>">
                            <?php endif; ?>
                            <?php if ($selectedAudience): ?>
                                <input type="hidden" name="audience" value="<?php echo htmlspecialchars($selectedAudience); ?>">
                            <?php endif; ?>
                             <?php if ($searchTerm): ?>
                                <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                            <?php endif; ?>
                            
                            <input 
                                type="range" 
                                name="max_price" 
                                min="0" 
                                max="500" 
                                x-model="currentMaxPrice"
                                @change="document.getElementById('price-filter-form').submit()"
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                
                            <div class="flex justify-between text-sm text-gray-500 mt-2">
                                <span>$0</span>
                                <span>$<span x-text="currentMaxPrice"></span></span>
                            </div>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <section class="w-full md:w-3/4 lg:w-4/5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                        <!-- Product Card -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                            <div class="relative">
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="block">
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full aspect-square object-cover transform group-hover:scale-105 transition-transform duration-300">
                                </a>
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center">
                                    <form action="add_to_cart.php" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                        <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                        <input type="hidden" name="image" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                                        <button type="submit" class="bg-primary text-white font-bold py-2 px-5 rounded-lg hover:bg-accent transition-colors">Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                            <div class="p-4">
                               <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="block">
                                    <h3 class="text-lg font-semibold text-primary hover:text-accent transition-colors truncate"><?php echo htmlspecialchars($product['name']); ?></h3>
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
        </div>
    </main>

    <!-- Footer (Same as index.php for consistency) -->
    <footer class="bg-primary text-white mt-16">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                 <!-- About Section -->
                 <div>
                    <h4 class="text-xl font-bold mb-4">ChicThreads</h4>
                    <p class="text-gray-400">Your destination for modern, high-quality fashion that makes you feel confident and stylish.</p>
                </div>
                <!-- Quick Links -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="shop.php" class="text-gray-400 hover:text-white transition-colors">Shop</a></li>
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

