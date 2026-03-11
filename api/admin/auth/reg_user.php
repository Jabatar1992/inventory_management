<?php

$method = "POST";
$cache  = "no-cache";
include "../../head.php"; 

if (isset($_POST['name'], $_POST['email'], $_POST['password'])) {

    $name     = cleanme(trim($_POST['name']));
    $email    = cleanme(trim($_POST['email']));
    $role     = isset($_POST['role']) ? cleanme(trim($_POST['role'])) : 'staff';
    $password = cleanme(trim($_POST['password']));

    
    // VALIDATION SECTION
    

    if (input_is_invalid($name) || input_is_invalid($email) || input_is_invalid($password)) {
        respondBadRequest("Name, Email and Password are required.");

    } else if (strlen($name) < 3) {
        respondBadRequest("Name must be at least 3 characters.");

    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respondBadRequest("Invalid email format.");

    } else if (!in_array($role, ['admin', 'staff'])) {
        respondBadRequest("Role must be either 'admin' or 'staff'.");

    } else if (strlen($password) < 8) {
        respondBadRequest("Password must be at least 8 characters.");

    } else if (!preg_match("/[A-Z]/", $password)) {
        respondBadRequest("Password must contain at least one uppercase letter.");

    } else if (!preg_match("/[\W]/", $password)) {
        respondBadRequest("Password must contain at least one special character.");

    } else {

        
        // CHECK IF EMAIL EXISTS
        

        $checkUser = $connect->prepare("SELECT id FROM user WHERE email = ?");
        $checkUser->bind_param("s", $email);
        $checkUser->execute();
        $result = $checkUser->get_result();

        if ($result->num_rows > 0) {
            respondBadRequest("User with this email already exists.");

        } else {

            
            // HASH PASSWORD
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            
            // INSERT NEW USER
            

            $insertUser = $connect->prepare("
                INSERT INTO user (name, email, role, password)
                VALUES (?, ?, ?, ?)
            ");

            $insertUser->bind_param("ssss", $name, $email, $role, $hashedPassword);
            $insertUser->execute();

            if ($insertUser->affected_rows > 0) {

                $user_id = $connect->insert_id;

                // Fetch inserted user details
                $getUser = $connect->prepare("
                    SELECT id, name, email, role, created_at
                    FROM user
                    WHERE id = ?
                ");
                $getUser->bind_param("i", $user_id);
                $getUser->execute();
                $userDetails = $getUser->get_result()->fetch_assoc();

                respondOK($userDetails, "User registered successfully");

            } else {
                respondBadRequest("User registration failed.");
            }
        }
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}

?>