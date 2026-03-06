<?php

$method = "GET";  
$cache  = "no-cache";
include "../head.php";

// ======================
// FETCH ALL PRODUCTS
// ======================

$query = $connect->prepare("
    SELECT id, name, sku, category, price, quantity, created_at
    FROM products
    ORDER BY created_at DESC
");

$query->execute();
$result = $query->get_result();

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

if (count($products) > 0) {
    respondOK($products, "Products fetched successfully");
} else {
    respondBadRequest("No products found.");
}

?>