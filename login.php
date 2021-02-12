<?php
session_start();
if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ){

    if (isset($_POST['uid']) && isset($_POST['pwd'])) {
        if (!empty($_POST['uid']) && !empty($_POST['pwd'])) {
            //The uid and password is not empty
            require '../conn/conn.php';

            $uid = mysqli_real_escape_string($conn, trim($_POST['uid']));
            $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);

            // Validate password strength
            $uppercase = preg_match('@[A-Z]@', $pwd);
            $lowercase = preg_match('@[a-z]@', $pwd);
            $number    = preg_match('@[0-9]@', $pwd);


            if (!$uppercase || !$lowercase || !$number) {
                //Password is not valid
                echo "The username or password you entered is wrong. Please try a different one.";
                $error = 1;
                die();
            }

            if (strlen($uid) < 3) {
                echo "The username or password you entered is wrong. Please try a different one.";
                $error = 1;
                die();
            }

            $sql = "SELECT * FROM `users` WHERE `uid` = ? OR `email` = ?;";
            $stmt = mysqli_stmt_init($conn);

            if (mysqli_stmt_prepare($stmt, $sql)) {

                mysqli_stmt_bind_param($stmt, 'ss', $uid, $uid);
                mysqli_stmt_execute($stmt);


                $result = mysqli_stmt_get_result($stmt);
                $result_check = mysqli_num_rows($result);
                
                if ($result_check > 0) {
                    //User exists
                    //Check if the password is correct

                    $sql = "SELECT * FROM `users` WHERE `uid` = ? OR `email` = ?";
                    $stmt = mysqli_stmt_init($conn);

                    if (mysqli_stmt_prepare($stmt, $sql)) {

                        mysqli_stmt_bind_param($stmt, 'ss', $uid, $uid);
                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt);
                        
                        if ($row = mysqli_fetch_assoc($result)) {
                            //Check if the passwords match

                            if (password_verify($pwd, $row['pwd'])) {
                                //The password is correct
                                //Log the user in
                                $_SESSION['id'] = $row['id'];
                                $_SESSION['uid'] = $row['uid'];
                                $_SESSION['email'] = $row['email'];
                                $_SESSION['invitedBy'] = $row['invitedBy'];
                                $_SESSION['invitation'] = $row['invitation'];
                                $_SESSION['earlyAccess'] = $row['earlyAccess'];
                                $_SESSION['browser'] = $row['browser'];
                                $_SESSION['confirmation'] = $row['confirmation'];
                            }else{
                                //Wrong password
                                //Do not log in the user
                                $error = 1;
                                echo "The username or password you entered is wrong. Please try a different one.";
                            }
                        }

                    }else {
                        //Statement is not ready
                        $error = 1;
                        echo "Failed to connect to the server. Please try again later.";
                    }


                }else {
                    //User dosn't exist
                    echo "The username or password you entered is wrong. Please try a different one.";
                    $error = 1;
                }

            }else {
                //Statement is not ready
                //Throw an unknown error
                echo "Failed to connect to the server. Please try again later.";
            }


        }else {
            //One of the inputs is empty
            //Throw a custom error
            $error = 0;
            echo "Please fill in all the inputs";
            $errorMsg = "Please fill in all the inputs";
        }
    }else {
        //Error, not all the post methods are send
        //Throw an error
        http_response_code(404);
        die();
    }

}else {
    http_response_code(404);
    die();
}
?>



<script type="text/javascript">


</script>