<?php

class update_user_info {

    private $conn;

    // constructor
    function __construct() {
        require_once 'android_login_connect.php';
        // connecting to database
        $db = new android_login_connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {

    }

    /**
     * Storing new user
     * returns user details
     */
    public function StoreUserInfo($first_name, $last_name, $reg_no, $email, $password, $gender) {
        $hash = $this->hashFunction($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt

        $stmt = $this->conn->prepare("INSERT INTO android_php_post(first_name, last_name, reg_no, email, 
        encrypted_password, salt, gender) VALUES(?, ?, ?, ?, ?, ?, ?)"); //? shows any datatype

        $stmt->bind_param("sssssss", $first_name, $last_name, $reg_no, $email, $encrypted_password, $salt, $gender);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT first_name, last_name, reg_no, email, encrypted_password, salt, gender
             FROM android_php_post WHERE reg_no = ?");
            $stmt->bind_param("s", $reg_no);
            $stmt->execute();
            $stmt-> bind_result($token2,$token3,$token4,$token5,$token6,$token7,$token8);
            while ( $stmt-> fetch() ) {
               $user["first_name"] = $token2;
               $user["last_name"] = $token3;
               $user["reg_no"] = $token4;
               $user["email"] = $token5;
               $user["gender"] = $token8;
            }
            $stmt->close();
            return $user;
        } else {
          return false;
        }
    }

    /**
     * Get user by registration number and password
     */
    public function VerifyUserAuthentication($reg_no, $password) {

        $stmt = $this->conn->prepare("SELECT first_name, last_name, reg_no, email, 
        encrypted_password, salt, gender FROM android_php_post WHERE reg_no = ?");

        $stmt->bind_param("s", $reg_no);

        if ($stmt->execute()) {
            $stmt-> bind_result($token2,$token3,$token4,$token5,$token6,$token7,$token8);

            while ( $stmt-> fetch() ) {
               $user["first_name"] = $token2;
               $user["last_name"] = $token3;
               $user["reg_no"] = $token4;
               $user["email"] = $token5;
               $user["encrypted_password"] = $token6;
               $user["salt"] = $token7;
               $user["gender"] = $token8;
            }

            $stmt->close();

            // verifying user password
            $salt = $token7;
            $encrypted_password = $token6;
            $hash = $this->CheckHashFunction($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }

    /**
     * Check if user exists or not
     */
    public function CheckExistingUser($email, $reg_no) {
        $stmt = $this->conn->prepare("SELECT email from android_php_post WHERE email = ?");

        $stmt = $this->conn->prepare("SELECT reg_no from android_php_post WHERE reg_no = ?");

        $stmt->bind_param("s", $email);
        $stmt->bind_param("s", $reg_no);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashFunction($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkHashFunction($salt, $password) {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }

}

?>