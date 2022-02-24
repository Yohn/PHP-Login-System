<?php
require_once("E:/php-guestbook.com/github/PHP-Login-System/assets/includes/config.php");

check_logged_out();

if (!isset($_POST['loginsubmit'])){

    header("Location: ../");
    exit();
}
else {

    /*
    * -------------------------------------------------------------------------------
    *   Securing against Header Injection
    * -------------------------------------------------------------------------------
    */

    foreach($_POST as $key => $value){

        $_POST[$key] = _cleaninjections(trim($value));
    }


    /*
    * -------------------------------------------------------------------------------
    *   Verifying CSRF token
    * -------------------------------------------------------------------------------
    */

    if (!verify_csrf_token()){

        $_SESSION['STATUS']['loginstatus'] = 'Request could not be validated';
        header("Location: ../");
        exit();
    }


    require '../../assets/setup/db.inc.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {

        $_SESSION['STATUS']['loginstatus'] = 'fields cannot be empty';
        header("Location: ./");
        exit();
    }
    else {

      /*
      * -------------------------------------------------------------------------------
      *   Updating last_login_at date('Y-m-d H:i:s') or NOW()
      * -------------------------------------------------------------------------------
      */
        $table='users';
        $index_field='username';
        $index_value=$username;
        $field_to_update='last_login_at';
        $data_to_update=date('Y-m-d H:i:s');
        $err=$ol->update_fieldPDO($table,$index_field,$index_value,$field_to_update,$data_to_update);
        /* $err =
        -1 - Query returned an error. Redundant if there is already error handling for execute()
         0 - No records updated on UPDATE, no rows matched the WHERE clause or no query been executed; just rows matched if PDO::MYSQL_ATTR_FOUND_ROWS => true
         1 - Greater than 0 - Returns number of rows affected;
        */
        if ($err<1) {

          $_SESSION['ERRORS']['nouser'] = 'username does not exist';
          header("Location: ../");
          exit();
        }


       /*
       * -------------------------------------------------------------------------------
       *   Get User Array from username
       * -------------------------------------------------------------------------------
       */
       $table='users';
     	 $search_field='username';
     	 $search_text=$username;
       $user_array=array();
     	 $row=$ol->get_arrayPDO($table,$search_field,$search_text);

        if (empty($row)) {

          $_SESSION['ERRORS']['nouser'] = 'username does not exist';
          header("Location: ../");
          exit();
        }
        else {

                $pwdCheck = password_verify($password, $row['password']);

                if ($pwdCheck == false) {

                    $_SESSION['ERRORS']['wrongpassword'] = 'wrong password';
                    header("Location: ../");
                    exit();
                    }
                else if ($pwdCheck == true) {

                    if (!session_id()) @session_start();


                    if($row['verified_at'] != NULL){

                        $_SESSION['auth'] = 'verified';
                    } else{

                        $_SESSION['auth'] = 'loggedin';
                    }
                    /*
                    * -------------------------------------------------------------------------------
                    *   Creating SESSION Variables
                    * -------------------------------------------------------------------------------
                    */
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['first_name'] = $row['first_name'];
                    $_SESSION['last_name'] = $row['last_name'];
                    $_SESSION['gender'] = $row['gender'];
                    $_SESSION['headline'] = $row['headline'];
                    $_SESSION['bio'] = $row['bio'];
                    $_SESSION['profile_image'] = $row['profile_image'];
                    //$_SESSION['banner_image'] = $row['banner_image'];
                    //$_SESSION['user_level'] = $row['user_level'];
                    $_SESSION['verified_at'] = $row['verified_at'];
                    $_SESSION['created_at'] = $row['created_at'];
                    $_SESSION['updated_at'] = $row['updated_at'];
                    $_SESSION['deleted_at'] = $row['deleted_at'];
                    $_SESSION['last_login_at'] = $row['last_login_at'];


                    /*
                    * -------------------------------------------------------------------------------
                    *   Setting rememberme cookie
                    * -------------------------------------------------------------------------------
                    */

                    if (isset($_POST['rememberme'])){

                        $selector = bin2hex(random_bytes(8));
                        $token = random_bytes(32);

                        $table='auth_tokens';
                        $search_field='user_email';
                        $search_text=$_SESSION['email'];
                        $err=$ol->delete_itPDO($table,$search_field,$search_text);

                        setcookie(
                            'rememberme',
                            $selector.':'.bin2hex($token),
                            time() + 864000,
                            '/',
                            NULL,
                            false, // TLS-only
                            true  // http-only
                        );

                        //$dbh=$ol->dbh();
                        $remember_me="remember_me";
                        $email=$_SESSION['email'];
                        $token=password_hash($token, PASSWORD_DEFAULT);
                        $d=date('Y-m-d\TH:i:s', time() + 864000);
                        $stmt = $dbh->prepare("insert into auth_tokens (`user_email`, `auth_type`, `selector`, `token`, `created_at`, `expires_at` ) values (:user_email, :auth_type, :selector, :token, :created_at, :expires_at )");
                           //$stmt->bindParam(':id', $id, PDO::PARAM_STR, 64);
                           $stmt->bindParam(':user_email', $email, PDO::PARAM_STR, 64);
                           $stmt->bindParam(':auth_type', $remember_me, PDO::PARAM_STR, 64);
                           $stmt->bindParam(':selector', $selector, PDO::PARAM_STR, 64);
                           $stmt->bindParam(':token',$token , PDO::PARAM_STR, 64);
                           $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR, 64);
                           $stmt->bindParam(':expires_at', $d, PDO::PARAM_STR, 64);
                        $executed = $stmt->execute();
                        if($executed){}else{
                            $_SESSION['ERRORS']['insert_failed'] = 'DB ERROR';
                            header("Location: ../");
                            exit();
                            }

                    }//end remember me
//http://localhost/php-guestbook.com/github/PHP-Login-System/login/includes/APP_URL/dashboard/
                    header("Location: ".APP_URL."/dashboard/");
                    exit();
                }// end else if ($pwdCheck == true)
            }// end else we have a username
        }// end empty($username) || empty($password

}//end _POST['loginsubmit
