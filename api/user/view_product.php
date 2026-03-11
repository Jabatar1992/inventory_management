<?php

$method = "GET";  
$cache  = "no-cache";
include "../head.php";


// FETCH PRODUCT BY PRODUCT_ID


if (isset($_GET['product_id'])) {

    $product_id = cleanme(trim($_GET['product_id']));

    
    // VALIDATION
    

    if (input_is_invalid($product_id)) {

        respondBadRequest("Product ID is required.");

    } else if (!is_numeric($product_id)) {

        respondBadRequest("Product ID must be numeric.");

    }

    
    // FETCH PRODUCT
    

    $query = $connect->prepare("
        SELECT id, name, sku, category, price, quantity, created_at
        FROM products
        WHERE id = ?
    ");

    $query->bind_param("i", $product_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {

        $product = $result->fetch_assoc();
        respondOK($product, "Product fetched successfully");

    } else {

        respondBadRequest("Product not found.");

    }

} else {

    respondBadRequest("Missing product_id parameter.");

}

?>