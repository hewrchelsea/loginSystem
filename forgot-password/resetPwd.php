<?php
session_start();
if (isset($_SESSION['uid'])) {
    http_response_code(404);
    die();
}

if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ){

    if (isset($_POST['selector']) && isset($_POST['validator']) && isset($_POST['pwd']) && isset($_POST['pwdConfirm'])) {

        require_once "../conn/conn.php";
        
        $selector = $_POST['selector'];
        $validator = $_POST['validator'];
        $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);
        $pwdConfirm = mysqli_real_escape_string($conn, $_POST['pwdConfirm']);

        $uppercase = preg_match('@[A-Z]@', $pwd);
        $lowercase = preg_match('@[a-z]@', $pwd);
        $number    = preg_match('@[0-9]@', $pwd);


        if (empty($pwd) || empty($pwdConfirm)) {
            //One of the password fields is empty
            echo "Please fill in all the inputs";
            die();
        }elseif ($pwd !== $pwdConfirm) {
            echo "The passwords don't match. Please try again.";
            die();
        }elseif (!$uppercase || !$lowercase || !$number || strlen($pwd) < 8) {
            echo 'Password should contain at least 8 characters and should include at least one upper case letter, one number, and one special character.';
            die();
        }

        $currentDate = date("U");

        $sql = "SELECT * FROM `pwdReset` WHERE `pwdResetSelector` = ? AND `pwdResetExpires` >= ?;";

        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $selector, $currentDate);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if (!$row = mysqli_fetch_assoc($result)) {
                echo "You need to re-submit your reset request.";
                exit();
            }else {
                
                $tokenBin = hex2bin($validator);
                $tokenCheck = password_verify($tokenBin, $row['pwdResetToken']);

                if ($tokenCheck === false) {
                    //Wrong token
                    echo "You need to re-submit your reset request.";
                    exit();
                }elseif ($tokenCheck === true) {
                    $email = $row['pwdResetEmail'];

                    $sql = "SELECT * FROM `users` WHERE `email` = ?";

                    $stmt = mysqli_stmt_init($conn);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $email);
                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt);

                        if (!$row = mysqli_fetch_assoc($result)) {
                            echo "There was an error. Please re-submit your password reset request.<br>" . $email ;
                            die();
                        }else {
                            if (password_verify($pwd, $row['pwd'])) {
                                //New and old passwords are the same
                                echo "New password cannot be same as old password.";
                            }else {
                                $sql = "UPDATE `users` SET `pwd` = ? WHERE email = ?";
                                $stmt = mysqli_stmt_init($conn);

                                if (mysqli_stmt_prepare($stmt, $sql)) {
                                    $pwdHash = password_hash($pwd, PASSWORD_DEFAULT);
                                    mysqli_stmt_bind_param($stmt, "ss", $pwdHash, $email);
                                    if (mysqli_stmt_execute($stmt)) {

                                        $sql = "DELETE FROM `pwdReset` WHERE `pwdResetEmail` = ?";
                                        $stmt = mysqli_stmt_init($conn);
                                        if (mysqli_stmt_prepare($stmt, $sql)) {
                                            mysqli_stmt_bind_param($stmt, "s", $email);
                                            mysqli_stmt_execute($stmt);
                                            echo "Password reset successfully.";
                                            mysqli_stmt_close($stmt);
                                            die();
                                        }else {
                                            //Not ready
                                            echo "There was an error!";
                                            die();
                                        }

                                    }else {
                                        echo "Error updating your password. Please re-submit your password reset request.";
                                        die();
                                    }
                                }else {
                                    //Not ready
                                    echo "Error updating your password. Please re-submit your password reset request.";
                                    die();
                                }
                            }
                        }

                    }else {
                        echo "There was an error.";
                        die();
                    }
                }
            }
        }else {
            //Not ready
            echo "Error. Please try again later.";
            die();
        }

    }else {
        //Not all the post methods are set
        //Return an error
        http_response_code(404);
        die();
    }
    
}else {
    //Not Ajax
    http_response_code(404);
    die();
}















?>