<?php
include_once 'connection.php';

$name = $_POST['type'];
$maxkg = $_POST['maxkg'];
$price = $_POST['price'];

$sql = "SELECT * FROM prices WHERE name = :name";
$stmt = $db->prepare($sql);
$stmt->bindParam(':name', $name);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    header('Location: ../profile.php?type=error&message='.$type.' is already exist');
    exit;
}

$sql = "INSERT INTO prices (name, maxkg, price) VALUES (:name, :maxkg, :price)";
$stmt = $db->prepare($sql);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':maxkg', $maxkg);
$stmt->bindParam(':price', $price);
$stmt->execute();

generate_logs('Adding Price', $name.'| New Price was added');
header('Location: ../profile.php?type=success&message=The price was added successfully');

?>