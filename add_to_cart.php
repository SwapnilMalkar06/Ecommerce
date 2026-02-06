<?php
session_start();

// Ensure the user is logged in before processing
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Check if the form was submitted with a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation to ensure required fields are present
    if (isset($_POST['product_id'], $_POST['name'], $_POST['price'], $_POST['image'])) {
        
        $productId = $_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        // Initialize the cart in the session if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // If the product is already in the cart, update its quantity
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            // Otherwise, add the new product to the cart
            $_SESSION['cart'][$productId] = [
                'id' => $productId,
                'name' => $_POST['name'],
                'price' => (float)$_POST['price'],
                'image' => $_POST['image'],
                'quantity' => $quantity
            ];
        }
    }
}

// After processing, redirect the user back to the page they came from.
// If the referring page isn't set, default to shop.php
$previousPage = $_SERVER['HTTP_REFERER'] ?? 'shop.php';
header('Location: ' . $previousPage);
exit();
?>
