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

// Check for a contact form submission message from the session
$formMessage = '';
$formMessageType = '';
if (isset($_SESSION['contact_form_message'])) {
    $formMessage = $_SESSION['contact_form_message'];
    $formMessageType = $_SESSION['contact_form_message_type'];
    // Unset the session variables so the message doesn't appear again on refresh
    unset($_SESSION['contact_form_message']);
    unset($_SESSION['contact_form_message_type']);
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ChicThreads</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;7-0&display=swap" rel="stylesheet">
    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] }, colors: { primary: '#1a1a1a', accent: '#f7a440', lightgray: '#f5f5f5' } } }
        }
    </script>
</head>
<body 
    x-data="{ 
        cartCount: <?php echo $cart_count; ?>, 
        showNotification: false, 
        notificationMessage: '', 
        notificationSuccess: false 
    }" 
    x-init="
        <?php if (!empty($formMessage)): ?>
            notificationMessage = '<?php echo addslashes($formMessage); ?>';
            notificationSuccess = '<?php echo $formMessageType; ?>' === 'success';
            showNotification = true;
            setTimeout(() => showNotification = false, 5000);
        <?php endif; ?>
    "
    class="bg-white text-primary font-sans antialiased">

    <!-- Notification Popup -->
    <div x-show="showNotification"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         @click.away="showNotification = false"
         class="fixed top-5 right-5 z-[60] p-4 rounded-lg shadow-lg text-white max-w-sm"
         :class="notificationSuccess ? 'bg-green-500' : 'bg-red-500'"
         style="display: none;">
        <p x-text="notificationMessage"></p>
    </div>

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
                <a href="about.php" class="text-gray-600 hover:text-accent transition-colors">About</a>
                <a href="contact.php" class="text-accent font-semibold transition-colors">Contact</a>
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
            <a href="about.php" class="block py-2 px-6 text-sm text-gray-600 hover:bg-lightgray hover:text-accent">About</a>
            <a href="contact.php" class="block py-2 px-6 text-sm text-accent font-semibold">Contact</a>
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
        <!-- Page Title Section -->
        <section class="bg-lightgray py-20 text-center">
            <div class="container mx-auto px-6">
                <h1 class="text-4xl md:text-5xl font-bold text-primary">Get In Touch</h1>
                <p class="text-lg text-gray-600 mt-4 max-w-3xl mx-auto">We'd love to hear from you! Whether you have a question about our products, an order, or just want to say hello, our team is ready to answer all your questions.</p>
            </div>
        </section>

        <!-- Contact Form and Details Section -->
        <section class="container mx-auto px-6 py-16">
            <div class="grid md:grid-cols-2 gap-12 items-start">
                <!-- Contact Information -->
                <div class="bg-lightgray p-8 rounded-lg">
                    <h2 class="text-3xl font-bold text-primary mb-6">Contact Information</h2>
                    <div class="space-y-6 text-gray-700">
                        <div class="flex items-start space-x-4">
                            <svg class="w-6 h-6 text-accent mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <div>
                                <h3 class="font-semibold">Our Address</h3>
                                <p>123 Fashion Ave, Style City, 11001</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                             <svg class="w-6 h-6 text-accent mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <div>
                                <h3 class="font-semibold">Email Us</h3>
                                <p>support@chicthreads.com</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <svg class="w-6 h-6 text-accent mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <div>
                                <h3 class="font-semibold">Call Us</h3>
                                <p>+1 (234) 567-890</p>
                            </div>
                        </div>
                    </div>
                     <div class="mt-8 rounded-lg overflow-hidden">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.622956294332!2d-73.98785368459393!3d40.74844097932788!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9b3117469%3A0xd134e199a405a163!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1663792985955!5m2!1sen!2sus" class="w-full rounded-lg" style="height: 450px; border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h2 class="text-3xl font-bold text-primary mb-6">Send Us a Message</h2>
                    <form action="handle_contact.php" method="POST" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" required class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" name="subject" id="subject" required class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea id="message" name="message" rows="4" required class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-accent focus:border-accent"></textarea>
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-accent transition-all duration-300 transform hover:scale-105">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

