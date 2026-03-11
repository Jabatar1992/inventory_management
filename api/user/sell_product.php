<?php

$method = "POST";
$cache  = "no-cache";
include "../head.php";


// REDUCE PRODUCT STOCK AFTER SALE

if (isset($_POST['product_id'], $_POST['quantity_sold'])) {

    $product_id     = cleanme(trim($_POST['product_id']));
    $quantity_sold  = cleanme(trim($_POST['quantity_sold']));

   
    // VALIDATION
    

    if (input_is_invalid($product_id) || input_is_invalid($quantity_sold)) {

        respondBadRequest("Product ID and quantity sold are required.");

    } else if (!is_numeric($product_id) || !is_numeric($quantity_sold)) {

        respondBadRequest("Product ID and quantity must be numeric.");

    }

   
    // CHECK PRODUCT STOCK
    

    $check = $connect->prepare("
        SELECT quantity
        FROM products
        WHERE id = ?
    ");

    $check->bind_param("i", $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        respondBadRequest("Product not found.");
    }

    $product = $result->fetch_assoc();
    $current_stock = $product['quantity'];

   
    // CHECK IF STOCK IS ENOUGH
    
    if ($quantity_sold > $current_stock) {

        respondBadRequest("Not enough stock available.");

    }

    
    // REDUCE STOCK
   

    $new_stock = $current_stock - $quantity_sold;

    $update = $connect->prepare("
        UPDATE products
        SET quantity = ?
        WHERE id = ?
    ");

    $update->bind_param("ii", $new_stock, $product_id);
    $update->execute();

    respondOK([], "Stock updated successfully. Remaining stock: ".$new_stock);

} else {

    respondBadRequest("Missing required parameters.");

}

?>