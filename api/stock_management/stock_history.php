<?php

$method = "GET";
$cache  = "no-cache";
include "../head.php";

// Optional filters: product_id, user_id
$product_id = isset($_GET['product_id']) ? cleanme(trim($_GET['product_id'])) : null;
$user_id    = isset($_GET['user_id']) ? cleanme(trim($_GET['user_id'])) : null;

// ======================
// BUILD QUERY
// ======================
$query = "SELECT sh.id, sh.product_id, p.name AS product_name, sh.change_type, sh.quantity, sh.user_id, u.name AS user_name, sh.created_at
          FROM stock_history sh
          INNER JOIN products p ON sh.product_id = p.id
          INNER JOIN users u ON sh.user_id = u.id
          WHERE 1=1";

$params = [];
$types  = "";

// Filter by product_id
if (!is_null($product_id)) {
    if (!is_numeric($product_id)) {
        respondBadRequest("Invalid Product ID.");
    }
    $query .= " AND sh.product_id = ?";
    $params[] = $product_id;
    $types .= "i";
}

// Filter by user_id
if (!is_null($user_id)) {
    if (!is_numeric($user_id)) {
        respondBadRequest("Invalid User ID.");
    }
    $query .= " AND sh.user_id = ?";
    $params[] = $user_id;
    $types .= "i";
}

$query .= " ORDER BY sh.created_at DESC";

// ======================
// EXECUTE QUERY
// ======================
$stmt = $connect->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

respondOK($history, "Stock history retrieved successfully.");

?>