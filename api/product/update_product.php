<?php

$method = "POST";
$cache  = "no-cache";
include "../head.php";

if (isset($_POST['product_id'], $_POST['name'], $_POST['sku'], $_POST['price'], $_POST['quantity'])) {

    $product_id = cleanme(trim($_POST['product_id']));
    $name       = cleanme(trim($_POST['name']));
    $sku        = cleanme(trim($_POST['sku']));
    $category   = isset($_POST['category']) ? cleanme(trim($_POST['category'])) : null;
    $price      = cleanme(trim($_POST['price']));
    $quantity   = cleanme(trim($_POST['quantity']));

    // ======================
    // VALIDATION SECTION
    // ======================

    if (input_is_invalid($product_id) || input_is_invalid($name) || input_is_invalid($sku) || input_is_invalid($price) || input_is_invalid($quantity)) {
        respondBadRequest("Product ID, Name, SKU, Price, and Quantity are required.");

    } else if (!is_numeric($product_id) || $product_id <= 0) {
        respondBadRequest("Invalid Product ID.");

    } else if (!is_numeric($price) || $price < 0) {
        respondBadRequest("Price must be a valid number.");

    } else if (!is_numeric($quantity) || $quantity < 0) {
        respondBadRequest("Quantity must be a valid number.");

    } else {

        // ======================
        // CHECK IF PRODUCT EXISTS
        // ======================

        $checkProduct = $connect->prepare("SELECT id FROM products WHERE id = ?");
        $checkProduct->bind_param("i", $product_id);
        $checkProduct->execute();
        $result = $checkProduct->get_result();

        if ($result->num_rows === 0) {
            respondBadRequest("Product not found.");
        } else {

            // ======================
            // UPDATE PRODUCT
            // ======================

            $updateProduct = $connect->prepare("
                UPDATE products
                SET name = ?, sku = ?, category = ?, price = ?, quantity = ?
                WHERE id = ?
            ");

            $updateProduct->bind_param("sssdis", $name, $sku, $category, $price, $quantity, $product_id);
            $updateProduct->execute();

            if ($updateProduct->affected_rows >= 0) {

                // Fetch updated product
                $getProduct = $connect->prepare("
                    SELECT id, name, sku, category, price, quantity, created_at
                    FROM products
                    WHERE id = ?
                ");
                $getProduct->bind_param("i", $product_id);
                $getProduct->execute();
                $productDetails = $getProduct->get_result()->fetch_assoc();

                respondOK($productDetails, "Product updated successfully");

            } else {
                respondBadRequest("Product update failed.");
            }
        }
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}
//
?>