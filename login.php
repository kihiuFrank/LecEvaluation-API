<?php
require_once 'update_user_info.php';
$db = new update_user_info();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['reg_no']) && isset($_POST['password'])) {
 
    // receiving the post params
    $reg_no = $_POST['reg_no'];
    $password = $_POST['password'];
 
    // get the user by reg_no and password
    $user = $db->VerifyUserAuthentication($reg_no, $password);
 
    if ($user != false) {
        // use is found
        $response["error"] = FALSE;
        $response["uid"] = $user["unique_id"]; //uniqid('ast');
        $response["user"]["first_name"] = $user["first_name"];
        $response["user"]["last_name"] = $user["last_name"];
        $response["user"]["reg_no"] = $user["reg_no"];
        $response["user"]["email"] = $user["email"];
        $response["user"]["gender"] = $user["gender"];
        echo json_encode($response);
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Your login credentials are wrong. Please try again!";
        echo json_encode($response);
    }
} else {
    // required post params is missing
    $response["error"] = TRUE; 
    $response["error_msg"] = "Required parameters reg_no or password is missing!";
    echo json_encode($response);
}
?> 