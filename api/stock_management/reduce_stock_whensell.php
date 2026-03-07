<?php

$method = "POST";
$cache  = "no-cache";
include "../head.php";

if (isset($_POST['product_id'], $_POST['quantity'])) {

    $product_id = cleanme(trim($_POST['product_id']));
    $quantity   = cleanme(trim($_POST['quantity']));
   // $user_id    = cleanme(trim($_POST['user_id']));

    $datasentin=ValidateAPITokenSentIN();
    $user_id=$datasentin->usertoken;



    // ======================
    // VALIDATION SECTION
    // ======================

    if (input_is_invalid($product_id) || input_is_invalid($quantity) || input_is_invalid($user_id)) {
        respondBadRequest("Product ID, Quantity, and User ID are required.");

    } else if (!is_numeric($product_id) || $product_id <= 0) {
        respondBadRequest("Invalid Product ID.");

    } else if (!is_numeric($quantity) || $quantity <= 0) {
        respondBadRequest("Quantity must be a positive number.");

    } else if (!is_numeric($user_id) || $user_id <= 0) {
        respondBadRequest("Invalid User ID.");

    } else {

        // ======================
        // CHECK IF PRODUCT EXISTS
        // ======================

        $checkProduct = $connect->prepare("SELECT id, quantity FROM products WHERE id = ?");
        $checkProduct->bind_param("i", $product_id);
        $checkProduct->execute();
        $productResult = $checkProduct->get_result();

        if ($productResult->num_rows == 0) {
            respondBadRequest("Product not found.");
        } else {

            $product = $productResult->fetch_assoc();

            // ======================
            // CHECK IF STOCK IS SUFFICIENT
            // ======================

            if ($quantity > $product['quantity']) {
                respondBadRequest("Insufficient stock. Available quantity: " . $product['quantity']);
            } else {
                $newQuantity = $product['quantity'] - $quantity;

                // ======================
                // UPDATE PRODUCT QUANTITY
                // ======================

                $updateProduct = $connect->prepare("UPDATE products SET quantity = ? WHERE id = ?");
                $updateProduct->bind_param("ii", $newQuantity, $product_id);
                $updateProduct->execute();

                if ($updateProduct->affected_rows > 0) {

                    // ======================
                    // INSERT INTO STOCK HISTORY
                    // ======================

                    $insertStock = $connect->prepare("
                        INSERT INTO stock_history (product_id, change_type, quantity, user_id)
                        VALUES (?, 'remove', ?, ?)
                    ");
                    $insertStock->bind_param("iii", $product_id, $quantity, $user_id);
                    $insertStock->execute();

                    if ($insertStock->affected_rows > 0) {
                        respondOK([
                            "product_id" => $product_id,
                            "reduced_quantity" => $quantity,
                            "new_quantity" => $newQuantity
                        ], "Stock reduced successfully.");
                    } else {
                        respondBadRequest("Failed to record stock history.");
                    }

                } else {
                    respondBadRequest("Failed to update product quantity.");
                }
            }
        }
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}

?>