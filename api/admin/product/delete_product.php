<?php

$method = "POST"; // using POST method for deletion
$cache = "no-cache";
include "../../head.php";

if (isset($_POST['product_id'])) {

    $product_id = cleanme(trim($_POST['product_id']));

    // ======================
    // VALIDATION SECTION
    // ======================

    if (input_is_invalid($product_id)) {
        respondBadRequest("Product ID is required.");

    } else if (!is_numeric($product_id)) {
        respondBadRequest("Product ID must be a number.");

    } else {

        // ======================
        // CHECK IF PRODUCT EXISTS
        // ======================

        $checkProduct = $connect->prepare("SELECT id, name FROM products WHERE id = ?");
        $checkProduct->bind_param("i", $product_id);
        $checkProduct->execute();
        $result = $checkProduct->get_result();

        if ($result->num_rows === 0) {
            respondBadRequest("Product not found.");
        } else {

            $productDetails = $result->fetch_assoc();

            // ======================
            // DELETE PRODUCT 
            // ======================

            $deleteProduct = $connect->prepare("DELETE FROM products WHERE id = ?");
            $deleteProduct->bind_param("i", $product_id);
            $deleteProduct->execute();

            if ($deleteProduct->affected_rows > 0) {
                respondOK($productDetails, "Product deleted successfully");
            } else {
                respondBadRequest("Failed to delete product.");
            }
        }
    }

} else {
    respondBadRequest("Invalid request. Product ID is required.");
}

?>