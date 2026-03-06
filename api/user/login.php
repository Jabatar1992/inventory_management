<?php
$method="POST";
$cache="no-cache";
include "../head.php";

// user id and password
if(isset($_POST['user_id']) && isset($_POST['password'])){
    $user_id=cleanme($_POST['user_id']);
    $password=cleanme($_POST['password']);
    //validation
    if(input_is_invalid($user_id) || input_is_invalid($password)){
        respondBadRequest("User ID and password are required");
    }else if(!is_numeric($user_id)){ 
        respondBadRequest("User ID must be numeric");
    }else {
        $getdataemail =  $connect->prepare("SELECT * FROM user where id=? and password=?"); 
        $getdataemail->bind_param("is",$user_id,$password);
        $getdataemail->execute();
        $getresultemail = $getdataemail->get_result();
        if( $getresultemail->num_rows> 0){
            $accesstoken=getTokenToSendAPI($user_id);
          respondOK(["access_token"=>$accesstoken],"Login successful");
        }else{ respondBadRequest(" user not found"); } 
 }
}else{
   respondBadRequest("Invalid request. User ID and password are required.");
}


















// if(isset($_POST['user_id'])){

//     $user_id = cleanme(trim($_POST['user_id']));

//     // validation
//     if(input_is_invalid($user_id)){
//         respondBadRequest("User ID is required");
//     }else if(!is_numeric($user_id)){ 
//         respondBadRequest("User ID must be numeric");
//     }else{

//         // check if user exists
//         $checkUser = $connect->prepare("SELECT * FROM user WHERE id=?");
//         $checkUser->bind_param("i", $user_id);
//         $checkUser->execute();
//         $result = $checkUser->get_result();

//         if($result->num_rows > 0){

           
//     $accesstoken=getTokenToSendAPI($user_id);

//     // Login successful
//     respondOK( ['access_token'=>$accesstoken],"Logout successful.");

// } else {
//     respondBadRequest("Invalid request. User ID and password are required.");
// }

//     }

// }
 
?>
