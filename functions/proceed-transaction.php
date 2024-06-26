<?php
include_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sql = "SELECT * FROM transactions WHERE user_id = :user_id AND status = 0 ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['id']);
$stmt->execute();
$results = $stmt->fetchAll();

$transaction_id = $results[0]['id'];

if (count($results) == 0){
    header('location: ../transaction.php?type=error&message=No transaction found!');
    exit();
}

$sql = "SELECT * FROM laundry WHERE transaction_id = :transaction_id AND status = 0";
$stmt = $db->prepare($sql);
$stmt->bindParam(':transaction_id', $transaction_id);
$stmt->execute();
$results = $stmt->fetchAll();

if (count($results) == 0){
    header('location: ../transaction.php?type=error&message=No laundry found!');
    exit();
}

$sql = "SELECT expenditures.id
        FROM expenditures
        JOIN items ON expenditures.item_id = items.id
        WHERE expenditures.transaction_id = :transaction_id AND user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':transaction_id', $transaction_id);
$stmt->bindParam(':user_id', $_SESSION['id']);
$stmt->execute();
$results = $stmt->fetchAll();

// if (count($results) == 0){
//     header('location: ../transaction.php?type=error&message=No items');
//     exit();
// }

// $sql = 'SELECT l.id, l.kilo, p.price AS price
//         FROM laundry AS l
//         JOIN transactions AS t ON l.transaction_id = t.id
//         JOIN prices AS p ON l.type = p.id
//         WHERE t.user_id = :user_id AND t.status = "pending"';
// $stmt = $db->prepare($sql);
// $stmt->bindParam(':user_id', $_SESSION['id']);
// $stmt->execute();
// $results = $stmt->fetchAll();

// $total_price = 0;
// foreach ($results as $row) {
//     $total_price += $row['price'] * $row['kilo'];
// }
$sql = 'SELECT l.id, l.kilo, p.price AS price
        FROM laundry AS l
        JOIN transactions AS t ON l.transaction_id = t.id
        JOIN prices AS p ON l.type = p.id
        WHERE t.user_id = :user_id AND t.status = "pending"';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['id']);
    $stmt->execute();
    $laundry_results = $stmt->fetchAll();

    $total_price = 0;
    foreach ($laundry_results as $row) {
        $total_price += $row['price'] * $row['kilo'];
    }

    $sql = "SELECT expenditures.id, expenditures.qty, items.name, items.price, (expenditures.qty * items.price) AS total
        FROM expenditures
        JOIN items ON expenditures.item_id = items.id
        WHERE expenditures.transaction_id = :transaction_id AND user_id = :user_id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':transaction_id', $id);
    $stmt->bindParam(':user_id', $_SESSION['id']);
    $stmt->execute();
    $item_results = $stmt->fetchAll();

    $item_total = 0;
    foreach ($item_results as $result){
        $item_total += $result['total'];
    }

    $total_laundry = $item_total + $total_price;

$amount = $_POST['amount'];
// $sql = "UPDATE laundry SET status = 1 WHERE transaction_id = :id AND status = 0";
// $stmt = $db->prepare($sql);
// $stmt->bindParam(':id', $transaction_id);
// $stmt->execute();

$sql = "UPDATE transactions SET total = :total, amount = :amount, status = 'completed' WHERE id = :id AND status = 'pending'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':total', $total_laundry);
$stmt->bindParam(':id', $transaction_id);
$stmt->bindParam(':amount', $amount);
$stmt->execute();

generate_logs('New Pending Transaction', $_SESSION['id'].' added a new pending transaction');
header('location: ../reciept.php?id='.$transaction_id);