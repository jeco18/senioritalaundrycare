<?php
include_once 'connection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user ID is set in session
if (!isset($_SESSION['id'])) {
    // Redirect if user ID is not set
    header('location: ../login.php?type=error&message=Please login to access this page');
    exit();
}

// Get the ID from the POST data
$id = $_POST['id'];

// Check if ID is empty
if (empty($id)) {
    header('location: ../transaction.php?type=error&message=Please select a customer!');
    exit();
}

// Check if user ID exists in the users table
$user_id = $_SESSION['id'];
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    // Redirect if user ID doesn't exist in the users table
    header('location: ../login.php?type=error&message=User not found');
    exit();
}

// Check if there is an existing pending transaction for the user
$sql = "SELECT * FROM transactions WHERE user_id = :user_id AND status = 'pending' ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$existing_transaction = $stmt->fetch();

if ($existing_transaction) {
    // Redirect if there is an existing pending transaction
    header('location: ../transaction.php?type=error&message=You have an existing transaction!');
    exit();
}

// Insert a new transaction
$sql = "INSERT INTO transactions (user_id, customer_id, status) VALUES (:user_id, :customer_id, 'pending')";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':customer_id', $id);
$stmt->execute();

// Log the transaction addition
generate_logs('Adding Transaction', 'New Transaction was added');

// Redirect with success message
header('location: ../transaction.php?type=success&message=Transaction added successfully!');
