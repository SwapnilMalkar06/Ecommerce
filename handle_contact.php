<?php
session_start();
require_once 'connection.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and retrieve form data
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $subject = trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING));
    $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));

    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Set an error message and redirect back
        $_SESSION['contact_form_message'] = 'Please fill out all fields correctly.';
        $_SESSION['contact_form_message_type'] = 'error';
        header('Location: contact.php');
        exit();
    }

    // Prepare an insert statement
    $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Set success message
            $_SESSION['contact_form_message'] = 'Thank you! Your message has been sent successfully.';
            $_SESSION['contact_form_message_type'] = 'success';
        } else {
            // Set error message
            $_SESSION['contact_form_message'] = 'Oops! Something went wrong. Please try again later.';
            $_SESSION['contact_form_message_type'] = 'error';
        }
        
        // Close statement
        $stmt->close();
    } else {
         $_SESSION['contact_form_message'] = 'Error preparing the database query. Please try again later.';
         $_SESSION['contact_form_message_type'] = 'error';
    }
    
    // Close connection
    $conn->close();

} else {
    // If not a POST request, just redirect
    $_SESSION['contact_form_message'] = 'Invalid request method.';
    $_SESSION['contact_form_message_type'] = 'error';
}

// Redirect back to the contact page
header('Location: contact.php');
exit();
?>

