<?php
include_once 'functions/connection.php';

$sql = 'SELECT t.id, 
               SUM(laundry_total) AS total_laundry,
               COALESCE(SUM(items_total), 0) AS total_items,
               t.created_at, 
               u.username, 
               c.fullname 
        FROM transactions AS t 
        JOIN customers AS c ON t.customer_id = c.id
        JOIN users AS u ON t.user_id = u.id 
        LEFT JOIN (
            SELECT l.transaction_id, 
                   SUM(p.price * l.kilo) AS laundry_total 
            FROM laundry AS l 
            JOIN prices AS p ON l.type = p.id 
            GROUP BY l.transaction_id
        ) AS lt ON t.id = lt.transaction_id
        LEFT JOIN (
            SELECT e.transaction_id, 
                   SUM(e.qty * i.price) AS items_total 
            FROM expenditures AS e 
            JOIN items AS i ON e.item_id = i.id 
            GROUP BY e.transaction_id
        ) AS it ON t.id = it.transaction_id
        WHERE t.status = "completed"
        GROUP BY t.id;'; 

$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll();
 
foreach ($results as $row) {
    echo '<tr>';
    ?>
        <td><a class="mx-1 text-decoration-none" target="_blank" href="reciept.php?id=<?php echo $row['id'] ?>&type=view"><i class="fas fa-print" style="font-size: 20px;"></i> <?= $row['id'] ?></a></td>
    <?php
    echo '<td><img class="rounded-circle me-2" width="30" height="30" src="assets/img/profile.png">' . $row['fullname'] . '</td>';
    echo '<td><img class="rounded-circle me-2" width="30" height="30" src="assets/img/profile.png">' . $row['username'] . '</td>';
    // Calculate total including both laundry and items
    $total = $row['total_laundry'] + $row['total_items'];
    echo '<td>₱' . number_format($total, 2) . '</td>';
    echo '<td>' . $row['created_at'] . '</td>';
    echo '</tr>';
}
?>
