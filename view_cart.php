<?php
// Always start the session on pages that need access to session variables.
session_start();

// This page assumes your `add_to_cart.php` script adds products to `$_SESSION['cart']`
// in an array format like this:
// $_SESSION['cart'][$product_id] = [
//     'name' => 'Product Name',
//     'price' => 10.99,
//     'quantity' => 1,
//     'image' => 'path/to/image.jpg' // Optional image path
// ];

// We will also need a simple script to handle item removal later.
// Let's call it `remove_from_cart.php`.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
    <!-- Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto mt-10 p-4">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 border-b pb-4">Your Shopping Cart</h1>

            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) : ?>
                <div class="flow-root">
                    <ul role="list" class="-my-6 divide-y divide-gray-200">
                        <?php
                        $total = 0;
                        // Loop through each item in the cart session
                        foreach ($_SESSION['cart'] as $id => $item) :
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <li class="flex py-6">
                                <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                    <!-- Using a placeholder image if no image is set -->
                                    <img src="<?php echo htmlspecialchars($item['image'] ?? 'https://placehold.co/100x100/e2e8f0/334155?text=Item'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="h-full w-full object-cover object-center">
                                </div>

                                <div class="ml-4 flex flex-1 flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>
                                                <a href="#"><?php echo htmlspecialchars($item['name']); ?></a>
                                            </h3>
                                            <p class="ml-4">$<?php echo number_format($subtotal, 2); ?></p>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">$<?php echo number_format($item['price'], 2); ?> each</p>
                                    </div>
                                    <div class="flex flex-1 items-end justify-between text-sm">
                                        <p class="text-gray-500">Qty <?php echo htmlspecialchars($item['quantity']); ?></p>

                                        <div class="flex">
                                            <!-- Link to remove the item. We will create remove_from_cart.php next -->
                                            <a href="remove_from_cart.php?id=<?php echo $id; ?>" class="font-medium text-indigo-600 hover:text-indigo-500">Remove</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Order Total -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <div class="flex justify-between text-lg font-bold text-gray-900">
                        <p>Total</p>
                        <p>$<?php echo number_format($total, 2); ?></p>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Shipping and taxes calculated at checkout.</p>
                    <div class="mt-6">
                        <a href="checkout.php" class="w-full flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">
                            Proceed to Checkout
                        </a>
                    </div>
                    <div class="mt-6 flex justify-center text-center text-sm text-gray-500">
                        <p>
                            or
                            <a href="index.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                                Continue Shopping
                                <span aria-hidden="true"> &rarr;</span>
                            </a>
                        </p>
                    </div>
                </div>

            <?php else : ?>
                <!-- This message shows if the cart is empty -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="mt-2 text-xl font-medium text-gray-900">Your cart is empty</h2>
                    <p class="mt-1 text-gray-500">Looks like you haven't added anything to your cart yet.</p>
                    <div class="mt-6">
                        <a href="index.php" class="text-base font-medium text-indigo-600 hover:text-indigo-500">
                            Start Shopping &rarr;
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
