<?php
session_start();



if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ){
    /*if (!isset($_POST['captcha']) || !isset($_SESSION['captcha'])) {
        $error = 'captcha';
        echo "Please solve the captcha";
        die();
    }*/


    if (isset($_POST['uid']) && isset($_POST['email']) && isset($_POST['pwd']) && isset($_POST['pwdCheck'])) {
        if (!empty($_POST['uid']) && !empty($_POST['email']) && !empty($_POST['pwd']) && !empty($_POST['pwdCheck'])) {
            require_once '../conn/conn.php';
            require_once './random.php';
            $uid = mysqli_real_escape_string($conn, trim($_POST['uid']));
            $email = mysqli_real_escape_string($conn, trim($_POST['email']));
            $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);
            $pwdCheck = mysqli_real_escape_string($conn, $_POST['pwdCheck']);

            $pwdHash = password_hash($pwd, PASSWORD_DEFAULT);

            if (preg_match('/^[A-Za-z0-9][A-Za-z0-9_]{2,29}$/', $_POST['uid'])) {
                
                if (fILTER_VAR($email, FILTER_VALIDATE_EMAIL)) {
                    
                    if ($pwd === $pwdCheck) {
                        
                        //Password validation


                        // Validate password strength
                        $uppercase = preg_match('@[A-Z]@', $pwd);
                        $lowercase = preg_match('@[a-z]@', $pwd);
                        $number    = preg_match('@[0-9]@', $pwd);

                        if(!$uppercase || !$lowercase || !$number || strlen($pwd) < 8) {
                            echo 'Password should contain at least 8 characters and should include at least one upper case letter, one number, and one special character.';
                            $errorMsg = 'Password should contain at least 8 characters and should include at least one upper case letter, one number, and one special character.';
                            $error = 3;
                        }else{
                            //Strong password

                            //Check if username is taken

                            $sql = "SELECT * FROM `users` WHERE uid=?";

                            $stmt = mysqli_stmt_init($conn);

                            if (mysqli_stmt_prepare($stmt, $sql)) {
                                mysqli_stmt_bind_param($stmt, 's', $uid);
                                mysqli_stmt_execute($stmt);

                                $result = mysqli_stmt_get_result($stmt);
                                $result_check = mysqli_num_rows($result);
                                
                                if ($result_check > 0) {
                                    //Username is taken
                                    echo "The username you entered is taken. Please try a different username.";
                                    $errorMsg = "The username you entered is taken. Please try a different username.";
                                }else {
                                    //Username is not taken
                                    //Check if the email is taken

                                    $sql = "SELECT * FROM users WHERE `email`=?";

                                    $stmt = mysqli_stmt_init($conn);

                                    if (mysqli_stmt_prepare($stmt, $sql)) {
                                        mysqli_stmt_bind_param($stmt, 's', $email);
                                        mysqli_stmt_execute($stmt);

                                        $result = mysqli_stmt_get_result($stmt);
                                        $result_check = mysqli_num_rows($result);

                                        if ($result_check > 0) {
                                            //Email is taken
                                            echo "The email you entered is taken. Please try a different email address.";
                                            $errorMsg = "The email you entered is taken. Please try a different email address.";
                                            $error = 2;
                                        }else {
                                            //Email is not taken
                                            //Check for captcha

                                            if (isset($_SESSION['captcha']) && isset($_POST['captcha'])) {
                                                

                                                if (!empty($_POST['captcha'])) {

                                                    if ($_POST['captcha'] === $_SESSION['captcha']) {

                                                        function mailingLsit ($conn, $email) {
                                                            $sql = "SELECT `browser` FROM `emails` WHERE email= ?;";
                                                            $stmt = mysqli_stmt_init($conn);
    
                                                            if (mysqli_Stmt_prepare($stmt, $sql)) {
                                                                
                                                                mysqli_stmt_bind_param($stmt, 's', $email);
                                                                if (mysqli_stmt_execute($stmt)) {
                                                                    $result = mysqli_stmt_get_result($stmt);
                                                                    $result_check = mysqli_num_rows($result);
                                                                    
                                                                    if ($result_check > 0) {
                                                                        $earlyAccess = 1;
                                                                        if ($row = mysqli_fetch_assoc($result)) {
                                                                            $browser = $row['browser'];
                                                                            $returnedValues = new StdClass;
                                                                            $returnedValues->mailingLsit = $earlyAccess;
                                                                            $returnedValues->browser = $browser;
                                                                            return $returnedValues;

                                                                        }
                                                                    }
                                                                }
    
    
                                                            }
                                                        }
                                                        $earlyAccess = 0;
                                                        $browser = 0;
                                                        if (!empty(mailingLsit($conn, $email))) {
                                                            if (is_object(mailingLsit($conn, $email))) {
                                                                $earlyAccess = mailingLsit($conn, $email)->mailingLsit;
                                                                $browser = mailingLsit($conn, $email)->browser;
                                                            }else {
                                                                $earlyAccess = 0;
                                                                $browser = 0;
                                                            }
                                                        }else {
                                                            $earlyAccess = 0;
                                                            $browser = 0;
                                                        }

                                                        $sql = "INSERT INTO `users` (`uid`, `email`, `pwd`, `invitation`, `inviteCount`, `earlyAccess`, `browser`) VALUES(?, ?, ?, ?, ?, ?, ?)";

                                                        $stmt = mysqli_stmt_init($conn);

                                                        if (mysqli_stmt_prepare($stmt, $sql)) {
                                                            $inviteCount = 0;
                                                            $random_ = random($conn);
                                                            echo "The browser number is:" . $browser;
                                                            echo "<br><br>The earlyAccess number is:" . $earlyAccess;

                                                            mysqli_stmt_bind_param($stmt, 'ssssiii', $uid, $email, $pwdHash, $random_, $inviteCount, $earlyAccess, $browser);
                                                            if (mysqli_stmt_execute($stmt)) {
                                                                //success
                                                                
                                                                if (isset($_POST['invation'])) {
                                                                    $invationCode = mysqli_real_escape_string($conn, trim($_POST['invation']));
                                                                    if (!empty($invationCode) && strlen($invationCode) == 4) {
                                                                        $sql = "SELECT * FROM `users` WHERE invitation = ?";
                                                                        $stmt = mysqli_stmt_init($conn);
                                                                        if (mysqli_stmt_prepare($stmt, $sql)) {
                                                                            mysqli_stmt_bind_param($stmt, 's', $invationCode);
                                                                            mysqli_stmt_execute($stmt);

                                                                            $result = mysqli_stmt_get_result($stmt);
                                                                            $result_check = mysqli_num_rows($result);

                                                                            if ($result_check > 0) {
                                                                                $sql = "UPDATE `users` SET `invitedBy` = ? WHERE `uid` = ?";
                                                                                $stmt = mysqli_stmt_init($conn);

                                                                                if (mysqli_stmt_prepare($stmt, $sql)) {
                                                                                    mysqli_stmt_bind_param($stmt, 'ss', $invationCode, $uid);
                                                                                    mysqli_stmt_execute($stmt);
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }

                                                                unset($_SESSION['captcha']);

                                                                $error = 7;
                                                                $errorMsg = 'You are signed up successfully. Check your email address to confirm your registration!';
                                                                
                                                            }else {
                                                                //Signup failed

                                                                echo "Signup failed. Please try again.";
                                                                $errorMsg = "Signup failed. Please try again.";
                                                                echo "<br>1<br>";
                                                                $error = 234;
                                                            }

                                                        }else {
                                                            echo "Failed to connect to the server. Please try again later.";
                                                            $errorMsg = "Failed to connect to the server. Please try again later.";
                                                            echo "<br>2<br>";
                                                            $error = 14;
                                                        }
                                                    }else {
                                                        //Wrong captcha
                                                        echo "Wrong captcha. Please try again.";
                                                        $errorMsg = "Wrong captcha. Please try again.";
                                                        $error = 6;
                                                    }
                                                }else {
                                                    //The captcha is empty
                                                    echo "Please solve the captcha.";
                                                    $errorMsg = "Please solve the captcha.";
                                                    $error = 6;
                                                }

                                            }else {
                                                //No captcha
                                                //Echo out 8 spaces, so that the javascript recogniziz the error.
                                                echo "        ";
                                                $errorMsg = "        ";
                                                $error = 5;
                                            }

                                        }
                                    }else {
                                        echo "Failed to connect to the server. Please try again later.";
                                        $errorMsg = "Failed to connect to the server. Please try again later.";
                                        $error = 312124;
                                        echo "<br>33<br>";
                                    }

                                }
                            }else {
                                echo "Failed to connect to the server. Please try again later.";
                                $errorMsg = "Failed to connect to the server. Please try again later.";
                                $error = 43232;
                                echo "<br>44312<br>";
                            }

                        }

                    }else {
                        echo "The two passwords you entered do not match. Please try again.";
                        $errorMsg = "The two passwords you entered do not match. Please try again.";
                        $error = 3;

                    }

                }else {
                    echo "The email you entered is invalid. Please try a different email address.";
                    $errorMsg = "The email you entered is invalid. Please try a different email address.";
                    $error = 2;
                }

            }else {
                //Invalid username
                $error = 1;
                echo "The username you entered is invalid. Please try a different username.";
                $errorMsg = "The username you entered is invalid. Please try a different username.";
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


var error = '<?php echo $error; ?>';

var errorMsg = '<?php echo $errorMsg; ?>';


var inputs = document.querySelectorAll('.signup-form input')

if (error == '0') {
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].value.trim().length == 0)
            inputs[i].classList.add('invalid')
    }
}else if (error == '1') {
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].classList.contains('uid')) {
            inputs[i].classList.add('invalid')
        }else {
            inputs[i].classList.remove('invalid')
        }
    }
}else if (error == '2') {
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].classList.contains('email')) {
            inputs[i].classList.add('invalid')
        }else {
            inputs[i].classList.remove('invalid')
        }
    }
}else if (error == '3') {
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].classList.contains('pwd') || inputs[i].classList.contains('pwdCheck')) {
            inputs[i].classList.add('invalid')
        }else {
            inputs[i].classList.remove('invalid')
        }
    }
}else if (error == '4' || error == '5') {
    for (let i = 0; i < inputs.length; i++) {
        inputs[i].classList.remove('invalid')
    }
}else if (error == '6') {
    for (let i = 0; i < inputs.length; i++) {
        inputs[i].classList.remove('invalid')
    }
    var input = document.querySelector('.captcha')
    input.classList.add('invalid')
}


</script>