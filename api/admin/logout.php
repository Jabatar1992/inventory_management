
<?php
$method="POST";
$cache="no-cache";
include "../head.php";

if(isset($_POST['admin_id'])){

    $admin_id = cleanme(trim($_POST['admin_id']));

    // validation
    if(input_is_invalid($admin_id)){
        respondBadRequest("admin ID is required");
    }else if(!is_numeric($admin_id)){ 
        respondBadRequest("admin ID must be numeric");
    }else{

        // check if admin exists
        $checkadmin = $connect->prepare("SELECT * FROM admin WHERE id=?");
        $checkadmin->bind_param("i", $admin_id);
        $checkadmin->execute();
        $result = $checkadmin->get_result();

        if($result->num_rows > 0){

           
    $accesstoken=getTokenToSendAPI($admin_id);

    // Login successful
    respondOK( ['access_token'=>$accesstoken],"Logout successful.");

} else {
    respondBadRequest("Invalid request. admin ID and password are required.");
}

    }

}

?>