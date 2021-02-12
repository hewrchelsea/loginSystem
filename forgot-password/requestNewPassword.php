<?php

if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ){


    if (isset($_POST['email'])) {

        require_once "../conn/conn.php";
        
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));

        if (!empty($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                //Check if email exists

                $sql = "SELECT NULL FROM `users` WHERE `email` = ?;";
                $stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($stmt, $sql)) {

                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);
                    $result_check = mysqli_num_rows($result);

                    if ($result_check > 0) {
                        //User found

                        $selector = bin2hex(random_bytes(8));
                        $token = random_bytes(32);
                        /*change this URL*/
                        $url = "127.0.0.1:81/forgot-password/createnew.php?selector=" . $selector . "&validator=" . bin2hex($token);
                        
                        $expiryDate = date("U") + 1800;

                        //Delete any existing password reset tokens

                        $sql = "DELETE FROM `pwdReset` WHERE `pwdResetEmail` = ?;";

                        $stmt = mysqli_stmt_init($conn);

                        if (mysqli_stmt_prepare($stmt, $sql)) {

                            mysqli_stmt_bind_param($stmt, "s", $email);
                            mysqli_stmt_execute($stmt);
                        }else {
                            //Not ready
                            echo "Error. Please try again later.";
                        }

                        $sql = "INSERT INTO `pwdReset` (`pwdResetEmail`, `pwdResetSelector`, `pwdResetToken`, `pwdResetExpires`) VALUES (?, ?, ?, ?);";

                        $stmt = mysqli_stmt_init($conn);

                        if (mysqli_stmt_prepare($stmt, $sql)) {
                            
                            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt, "ssss", $email, $selector, $hashedToken, $expiryDate);
                            mysqli_stmt_execute($stmt);



                        }else {
                            //Not prepared
                            echo "Error. Please try again later.";
                            die();
                        }

                        mysqli_stmt_close($stmt);

                        require_once "../sendEmail/sendEmail.php";

                        $from = "Hewrchelsea@yahoo.com";
                        $to = $email;
                        $subject = "Password Change";

                        $html = '
                            <!DOCTYPE html>
                            <html lang="en" style="font-size: 62.5%;">

                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">

                                <style>
                                    html{
                                        background-color: rgb(300, 300, 300);
                                        font-family: sans-serif;
                                    }
                                    body{
                                        min-height: 619px;
                                    }
                                    *{
                                        padding: 0!important;
                                        margin: 0!important;
                                        box-sizing: border-box!important;
                                    }
                                    @media (max-width:900px) {
                                        html{
                                            font-size: 50%!important;
                                        }
                                    }
                                    @media (max-width:500px) {
                                        html{
                                            font-size: 40%!important;
                                        }
                                        .logo{
                                            height: 40px;
                                        }
                                    }
                                </style>
                            </head>

                            <body>
                                <table id="header" style="
                                    width: 90%;
                                    height: 100px;
                                    margin: 0 auto!important;
                                
                                ">
                                    <tr>
                                        <th style="
                                            text-align: left;
                                            height: 100%!important;
                                        ">
                                            <img src="https://i.ibb.co/3Yxq4T1/logo-white.png" alt="logo.png" style="
                                                max-height: 50px;
                                            " class="logo">
                                        </th>
                                    </tr>
                                </table>


                                <table id="main" style="
                                    width: 90%;
                                    height: auto;
                                    margin: 0 auto!important;
                                    text-align: left;
                                ">
                                    <tr>
                                        <th>
                                            <h1 style="
                                                font-size:3rem;
                                                margin-bottom: 20px!important;
                                            ">
                                                You have requested a passowrd reset
                                            </h1>
                                        </th>
                                    </tr>

                                    <tr>
                                        <td>
                                            <p style="
                                                font-size: 2.3rem;
                                                line-height: 180%;
                                            ">
                                                This email is sent to you because you have Requested a password reset.<br>
                                                If you haven\'t, simply ignore this email.<br>
                                                Password reset link:<br>
                                                <a href="' .$url. '" style="
                                                    padding-bottom:2px!important;
                                                    color: #0077cc;
                                                    border-bottom: 2.5px solid #0077cc;
                                                    text-decoration: none;
                                                ">' .$url. '</a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                </body>
                            </html>

                        ';

                        sendEmail ($from, $to, $subject, $html);

                        echo "Check your email.";
                        echo "<script type=\"text/javascript\">
                                    var success = 2
                        </script> ";

                        

                    }else {
                        //User not found
                        echo "The email is not found. Please try a different email address.";
                        $error = 1;
                    }

                }else {
                    //Not ready
                    echo "Error. Please try again later.";
                }
            }else {
                //Email is invalid
                echo "The email is not found. Please try a different email address.";
                $error = 1;
                
            }
        }else {
            //empty   
            echo "Please fill in the input.";
        }

    }else {
        //No post method
        //Return an error

        http_response_code(404);
        die();
    }

}else {
    //Not XMLHttpRequest

    http_response_code(404);
    die();
}

?>