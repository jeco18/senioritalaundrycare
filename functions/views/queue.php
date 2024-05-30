<?php
include_once 'functions/connection.php';

$sql = 'SELECT l.status, l.id AS laundry_id, l.kilo, p.price, p.name, p.unit, t.id AS transaction_id, t.customer_id, t.total, l.created_at, c.fullname, c.contact
        FROM laundry AS l
        JOIN prices AS p ON l.type = p.id
        JOIN transactions AS t ON l.transaction_id = t.id
        JOIN customers AS c ON t.customer_id = c.id
        WHERE l.status >= 0 AND l.status < 4
        ORDER BY t.id';

$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll();

$transaction_totals = [];

// Group results by transaction ID and calculate total for each group
foreach ($results as $row) {
    $transaction_id = $row['transaction_id'];

    // Initialize items total
    $items_total = 0;

    // Calculate total laundry price for each laundry item
    $laundry_total = $row['price'] * $row['kilo'];

    // Calculate total items price for each transaction
    $sql_items = "SELECT SUM(e.qty * i.price) AS items_total
                    FROM expenditures AS e
                    JOIN items AS i ON e.item_id = i.id
                    WHERE e.transaction_id = :transaction_id";
    $stmt_items = $db->prepare($sql_items);
    // Bind the correct transaction ID
    $stmt_items->bindParam(':transaction_id', $transaction_id);
    $stmt_items->execute();
    $items_total_result = $stmt_items->fetch(PDO::FETCH_ASSOC);
    if ($items_total_result) {
        $items_total = $items_total_result['items_total'];
    }

    // Calculate total including laundry and items
    $total = $laundry_total + $items_total;

    // Store the total for each transaction ID
    if (!isset($transaction_totals[$transaction_id])) {
        $transaction_totals[$transaction_id] = $total;
    } else {
        // If transaction ID already exists, add the total to existing total
        $transaction_totals[$transaction_id] += $total - $items_total;
    }
}

// Loop through grouped results and display in table
foreach ($results as $row) {
    $transaction_id = $row['transaction_id'];

    // Determine status
    if ($row['status'] == 0) {
        $status = 'Pending';
    } else if ($row['status'] == 1) {
        $status = 'Processing';
    } else if ($row['status'] == 2) {
        $status = 'Folding';
    } else if ($row['status'] == 3) {
        $status = 'Ready for Pickup';
    } else if ($row['status'] == 4) {
        $status = 'Claimed';
    } else {
        $status = 'Unknown';
    }

    // Output table row
    ?>
    <tr>
        <td><?php echo $transaction_id; ?></td>
        <td><img class="rounded-circle me-2" width="30" height="30" src="assets/img/profile.png"><?php echo $row['fullname']; ?></td>
        <td><?php echo $row['kilo'] . ' ' . $row['unit'] ?></td>
        <td><?php echo $status ?></td>
        <td class="text-center">
            <?php if ($row['status'] < 5): ?>
                <a class="mx-1 text-decoration-none text-success" href="#" data-bs-target="#up" data-bs-toggle="modal" data-id="<?php echo $row['laundry_id']?>"><i class="far fa-arrow-alt-circle-up text-success" style="font-size: 20px;"></i></a>
                <a class="mx-1 text-decoration-none <?php if($row['status'] <= 1) { echo 'd-none';}?>" href="#" data-bs-target="#down" data-bs-toggle="modal" data-id="<?php echo $row['laundry_id']?>"><i class="far fa-arrow-alt-circle-down" style="font-size: 20px;"></i></a>
                <a class="mx-1 text-decoration-none" href="tracking.php?id=<?php echo $transaction_id; ?>" target="_blank"><i class="far fa-credit-card" style="font-size: 20px;"></i></a>
                <a class="mx-1 text-decoration-none" href="invoice.php?id=<?php echo $transaction_id; ?>" target="_blank"><i class="fas fa-print" style="font-size: 20px;"></i></a>
                <!-- Add Infobip Message Form Modal Trigger -->
                <a class="mx-1 text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#messageModal_<?php echo $row['laundry_id']; ?>">
                    <i class="fas fa-envelope" style="font-size: 20px;"></i>
                </a>
                <!-- Infobip Message Form Modal -->
                <div class="modal fade" id="messageModal_<?php echo $row['laundry_id']; ?>" tabindex="-1" aria-labelledby="messageModalLabel_<?php echo $row['laundry_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="messageModalLabel_<?php echo $row['laundry_id']; ?>">Send Message to <?php echo $row['fullname']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Infobip Message Form Goes Here -->
                                <!-- You can customize this form as needed -->
                                <form action="send-message.php" method="post">
                                    <input style="display: none;"  name="contact" id="contact" value="<?php echo $row['contact']; ?>">
                                    <div class="mb-3">
                                        <label for="message" class="form-label text-secondary">Message</label>
                                        <!-- Use the transaction ID to fetch the total amount from the array -->
                                        <textarea class="form-control text-reset" id="message" name="message" style="height: 100px" readonly>Hello <?php echo $row['fullname']; ?>, &#13;&#10;Your laundry is now ready for pickup with a total price of ₱<?php echo number_format($transaction_totals[$transaction_id], 2); ?>. Thank you!</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a class="mx-1" href="#"><i class="far fa-circle text-warning" style="font-size: 20px;"></i></a>
                <a class="mx-1" href="#"><i class="far fa-circle text-warning" style="font-size: 20px;"></i></a>
                <a class="mx-1" href="tracking.php?id=<?php echo $transaction_id; ?>" target="_blank"><i class="far fa-credit-card" style="font-size: 20px;"></i></a>
                <a class="mx-1" href="#" role="button" data-bs-target="#confirm" data-bs-toggle="modal" data-id="<?php echo $transaction_id; ?>"><i class="far fa-trash-alt text-danger" style="font-size: 20px;"></i></a>
            <?php endif; ?>
        </td>
    </tr>
<?php
}
?>
