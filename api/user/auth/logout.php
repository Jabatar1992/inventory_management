
<?php
$method="POST";
$cache="no-cache";
include "../head.php";

if(isset($_POST['user_id'])){

    $user_id = cleanme(trim($_POST['user_id']));

    // validation
    if(input_is_invalid($user_id)){
        respondBadRequest("User ID is required");
    }else if(!is_numeric($user_id)){ 
        respondBadRequest("User ID must be numeric");
    }else{

        // check if user exists
        $checkUser = $connect->prepare("SELECT * FROM user WHERE id=?");
        $checkUser->bind_param("i", $user_id);
        $checkUser->execute();
        $result = $checkUser->get_result();

        if($result->num_rows > 0){

           
    $accesstoken=getTokenToSendAPI($user_id);

    // Login successful
    respondOK( ['access_token'=>$accesstoken],"Logout successful.");

} else {
    respondBadRequest("Invalid request. User ID and password are required.");
}

    }

}

?>