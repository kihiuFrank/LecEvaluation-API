<?php
  
require_once 'update_user_info.php';
$db = new update_user_info();
  
// json response array
$response = array("error" => FALSE);
  
if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['reg_no']) && isset($_POST['email']) && 
isset($_POST['password']) && isset($_POST['gender'])) {
 
    // receiving the post params
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $reg_no = $_POST['reg_no'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
 
    // check if user is already exists with the same email
    if ($db->CheckExistingUser($email,$reg_no)) {
        // user already exists
        $response["error"] = TRUE;
        $response["error_msg"] = "Email already exists with " . $email;
        $response["error_msg"] = "Reg-no already exists with " . $reg_no;
        echo json_encode($response);
    } else {
        // create a new user
        $user = $db->StoreUserInfo($first_name, $last_name, $reg_no, $email, $password, $gender);
        if ($user) {
            // user stored successfully
            $response["error"] = FALSE;
            $response["user"]["first_name"] = $user["first_name"];
            $response["user"]["last_name"] = $user["last_name"];
            $response["user"]["reg_no"] = $user["reg_no"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["gender"] = $user["gender"];
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = TRUE; 
            $response["error_msg"] = "Unknown error occurred in registration!";
            echo json_encode($response);
        }
     }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (first_name, last_name, reg_no, email or password) is missing!";
    echo json_encode($response);
}
?>