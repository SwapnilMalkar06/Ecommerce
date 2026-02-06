<?php
// Always start the session on pages that need access to session variables.
session_start();

// Check if the product ID is provided in the URL query string.
if (isset($_GET['id'])) {
    // Sanitize the ID to be safe, although in this context it's an array key.
    $id_to_remove = $_GET['id'];

    // Check if the item actually exists in the cart before trying to remove it.
    if (isset($_SESSION['cart'][$id_to_remove])) {
        // Use unset() to remove the item from the session cart array.
        unset($_SESSION['cart'][$id_to_remove]);
    }
}

// After processing, redirect the user back to the shopping cart page.
// This ensures that even if someone navigates to this script directly
// without an ID, they will just be sent back to the cart.
header('Location: view_cart.php');
exit(); // It's good practice to call exit() after a header redirect.
?>
