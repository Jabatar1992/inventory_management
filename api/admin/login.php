<?php
$method="POST";
$cache="no-cache";
include "../head.php";

// admin id and password
if(isset($_POST['admin_id']) && isset($_POST['password'])){
    $admin_id=cleanme($_POST['admin_id']);
    $password=cleanme($_POST['password']);
    //validation
    if(input_is_invalid($admin_id) || input_is_invalid($password)){
        respondBadRequest("admin ID and password are required");
    }else if(!is_numeric($admin_id)){ 
        respondBadRequest("admin ID must be numeric");
    }else {
        $getdataemail =  $connect->prepare("SELECT * FROM admin where id=? and password=?"); 
        $getdataemail->bind_param("is",$admin_id,$password);
        $getdataemail->execute();
        $getresultemail = $getdataemail->get_result();
        if( $getresultemail->num_rows> 0){
            $accesstoken=getTokenToSendAPI($admin_id);
          respondOK(["access_token"=>$accesstoken],"Login successful");
        }else{ respondBadRequest(" admin not found"); } 
 }
}else{
   respondBadRequest("Invalid request. admin ID and password are required.");
} 
 







?>
