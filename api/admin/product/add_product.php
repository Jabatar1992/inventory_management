<?php

$method = "POST";
$cache  = "no-cache";
include "../../head.php";

if (isset($_POST['name'], $_POST['sku'], $_POST['price'], $_POST['quantity'])) {

    $name     = cleanme(trim($_POST['name']));
    $sku      = cleanme(trim($_POST['sku']));
    $category = isset($_POST['category']) ? cleanme(trim($_POST['category'])) : null;
    $price    = cleanme(trim($_POST['price']));
    $quantity = cleanme(trim($_POST['quantity']));


    // VALIDATION SECTION


    if (input_is_invalid($name) || input_is_invalid($sku) || input_is_invalid($price) || input_is_invalid($quantity)) {
        respondBadRequest("Name, SKU, Price and Quantity are required.");

    } else if (!is_numeric($price) || $price < 0) {
        respondBadRequest("Price must be a valid number.");

    } else if (!is_numeric($quantity) || $quantity < 0) {
        respondBadRequest("Quantity must be a valid number.");

    } else {

        // ======================
        // CHECK IF SKU EXISTS
        // ======================

        $checkProduct = $connect->prepare("SELECT id FROM products WHERE sku = ?");
        $checkProduct->bind_param("s", $sku);
        $checkProduct->execute();
        $result = $checkProduct->get_result();

        if ($result->num_rows > 0) {
            respondBadRequest("Product with this SKU already exists.");

        } else {

            // ======================
            // INSERT NEW PRODUCT
            // ======================

            $insertProduct = $connect->prepare("
                INSERT INTO products (name, sku, category, price, quantity)
                VALUES (?, ?, ?, ?, ?)
            ");

            $insertProduct->bind_param("sssdi", $name, $sku, $category, $price, $quantity);
            $insertProduct->execute();

            if ($insertProduct->affected_rows > 0) {

                $product_id = $connect->insert_id;

                // Fetch inserted product
                $getProduct = $connect->prepare("
                    SELECT id, name, sku, category, price, quantity, created_at
                    FROM products
                    WHERE id = ?
                ");

                $getProduct->bind_param("i", $product_id);
                $getProduct->execute();
                $productDetails = $getProduct->get_result()->fetch_assoc();

                respondOK($productDetails, "Product added successfully");

            } else {
                respondBadRequest("Failed to add product.");
            }
        }
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}

?>