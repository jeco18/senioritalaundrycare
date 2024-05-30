<?php
include_once 'functions/connection.php';

function customer_list (){
    global $db;
    $sql = 'SELECT * FROM customers ORDER BY fullname ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();

    foreach ($results as $row) {
    $fullname = $row['fullname'];
    ?>
        <option value="<?php echo $row['id']; ?>"><?php echo $fullname; ?></option>
    <?php
    }
}

function items_list (){
    global $db;
    $sql = 'SELECT * FROM items WHERE stock > 0 ORDER BY name ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();

    foreach ($results as $row) {
    $name = $row['name'];
    ?>
        <option value="<?php echo $row['id']; ?>"><?php echo $name; ?> | Qty: <?php echo $row['stock']; ?></option>
    <?php
    }
}


function regular_price_list(){
    global $db;
    $sql = 'SELECT * FROM prices WHERE name LIKE "%regular%" ORDER BY name ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        $name = $row['name'];
?>
        <option value="<?php echo $row['id']; ?>" data-unit="<?php echo $row['unit']; ?>"><?php echo $name; ?> | ₱<?php echo $row['price']; ?></option>
<?php
    }
}

function whites_price_list(){
    global $db;
    $sql = 'SELECT * FROM prices WHERE name LIKE "%whites%" ORDER BY name ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        $name = $row['name'];
?>
        <option value="<?php echo $row['id']; ?>" data-unit="<?php echo $row['unit']; ?>"><?php echo $name; ?> | ₱<?php echo $row['price']; ?></option>
<?php
    }
}

function towel_price_list(){
    global $db;
    $sql = 'SELECT * FROM prices WHERE name LIKE "%towel%" ORDER BY name ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        $name = $row['name'];
?>
        <option value="<?php echo $row['id']; ?>" data-unit="<?php echo $row['unit']; ?>"><?php echo $name; ?> | ₱<?php echo $row['price']; ?></option>
<?php
    }
}

function beddings_price_list(){
    global $db;
    $sql = 'SELECT * FROM prices WHERE name LIKE "%beddings%" ORDER BY name ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        $name = $row['name'];
?>
        <option value="<?php echo $row['id']; ?>" data-unit="<?php echo $row['unit']; ?>"><?php echo $name; ?> | ₱<?php echo $row['price']; ?></option>
<?php
    }
}

function maong_price_list(){
    global $db;
    $sql = 'SELECT * FROM prices WHERE name LIKE "%maong%" ORDER BY name ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        $name = $row['name'];
?>
        <option value="<?php echo $row['id']; ?>" data-unit="<?php echo $row['unit']; ?>"><?php echo $name; ?> | ₱<?php echo $row['price']; ?></option>
<?php
    }
}

function comforters_price_list(){
    global $db;
    $sql = 'SELECT * FROM prices WHERE name LIKE "%comforters%" ORDER BY name ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        $name = $row['name'];
?>
        <option value="<?php echo $row['id']; ?>" data-unit="<?php echo $row['unit']; ?>"><?php echo $name; ?> | ₱<?php echo $row['price']; ?></option>
<?php
    }
}

function fold_price_list(){
    global $db;
    $sql = 'SELECT * FROM prices WHERE name LIKE "%fold%" ORDER BY name ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        $name = $row['name'];
?>
        <option value="<?php echo $row['id']; ?>" data-unit="<?php echo $row['unit']; ?>"><?php echo $name; ?> | ₱<?php echo $row['price']; ?></option>
<?php
    }
}


function get_transaction ($id){
    global $db;
    // Need to be fixed 7/15/2023
    $sql = 'SELECT t.id, c.fullname, c.address, c.contact
            FROM transactions AS t
            JOIN customers AS c ON t.customer_id = c.id
            WHERE t.status = "pending" AND t.user_id = '.$id.';
            ';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    return $results;
}