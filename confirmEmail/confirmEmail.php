<?php

session_start();

function email ($email, $token) {
    require_once "../sendEmail/sendEmail.php";

    $from = "Hewrchelsea@yahoo.com";
    $to = $email;
    $subject = "Email Confirmation";

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
                            Email confirmation
                        </h1>
                    </th>
                </tr>

                <tr>
                    <td>
                        <p style="
                            font-size: 2.3rem;
                            line-height: 180%;
                        ">
                            This email is sent to you because you have to confirm your email address.<br>
                            Confrimation code:<br>
                            <p>' .$token. '</p>
                        </p>
                    </td>
                </tr>
            </table>
            </body>
        </html>

    ';

    sendEmail($from, $to, $subject, $html);

    echo "The confirmation codes associated with your account were either invalid, or expired. We have send you a new one. Check your email!";


}




function sendNewToken ($email, $conn) {

    $sql = "SELECT * FROM `emailConfirmation` WHERE `email` = ?;";

    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            //There is already a token, that is expired
            //Deleting the existing token
            $sql = "DELETE FROM `emailConfirmation` WHERE `email` = ?";
            $stmt = mysqli_stmt_init($conn);
        
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
            }else {
                //Not ready
                echo "Error. Please try again later.";
                die();
            }

            //Create and store the new token
            $token = mt_rand(100000, 999999);
            $tokenHash = password_hash($token, PASSWORD_DEFAULT);
            $expires =  date("U") + 1800;
            $failedAttemp = 0;

            $sql = "INSERT INTO `emailConfirmation` (`email`, `code`, `expires`, `failedAttemp`) VALUES(?, ?, ?, ?);";
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssii", $email, $tokenHash, $expires, $failedAttemp);
                mysqli_stmt_execute($stmt);

                //Sendnig the email
                email($email, $token);
                mysqli_stmt_close($stmt);
                die();
            }else {
                //Not ready
                echo "Error. Please try again later.";
                die();
            }

        }else {
            /*
            
                Ignore for now
            
            */
            die();
        }


    }else {
        //Not ready
        echo "Error. Please try again later.";
        die();
    }


}


if (!isset($_SESSION['email']))
    header("Location: ../login") or die();

if ($_SESSION['confirmation'] == 1) {
    http_response_code(404);
    die();
}
if( !isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) || ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ) ){
    http_response_code(404);
    die();
}
if (!isset($_POST['code'])) {
    http_response_code(404);
    die();
}
$email = $_SESSION['email'];


if (empty(trim($_POST['code']))) {
    echo "Please fill in all the inputs";
}else {
    require_once "../conn/conn.php";

    $token = mysqli_real_escape_string($conn, $_POST['code']);

    if (!is_numeric($token) || strlen($token) != 6 ) {
        //Token is invalid, meaning it doesn't exist
        
        

        $sql = "SELECT * FROM `emailConfirmation` WHERE email = ?;";
        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                
                $currentDate = date("U");
                
                if ($row['expires'] < $currentDate || $row['failedAttemp'] >= 10) {
                    //Send a new token
                    sendNewToken($email, $conn);
                    echo "The cofirmation codes that were associated with your account were invalid. We have sent you a new confirmation code. Check your email!";
                    die();
                }
            }else {
                sendNewToken($email, $conn);
                echo "We have sent you a new token. Check your email!";
                die();
            }

        }else {
            //Not ready
            echo "Error. Please try again.";
            die();
        }



        

        echo "The confirmation code is wrong. Please double check it, and try again.";
        $sql = "UPDATE `emailConfirmation` SET `failedAttemp` = `failedAttemp` + 1 WHERE email = ?;";

        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

        }else {
            //Not ready
            echo "Error. Please try again.";
            die();
        }
        die();
    }
    //Check if the token exists
    $sql = "SELECT * FROM `emailConfirmation` WHERE `email` = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);


        if (!$row = mysqli_fetch_assoc($result)) {
            echo "No connfirmation code found. Please <a class=\"real-link\" href=\"javascript:void(0)\">request a new one.</a>";
            die();
        }else {
            //Check if token has expired
            $currentDate = date("U");
            if ($row['expires'] < $currentDate) {
                echo "Confirmation code has expired. we have sent you a new one. Check your email!";
                sendNewToken($email, $conn);
                die();
            }
            if ($row['failedAttemp'] >= 10) {
                echo "Too many failed attemp, We have send you a new confirmation code. Check your email!";
                sendNewToken($email, $conn);
                die();
            }

            $check = password_verify($token, $row['code']);
            if ($check == false) {
                //Wrong confirmation code
                echo "The confirmation code you entered is wrong. Please try a different one.";
                
                $sql = "UPDATE `emailConfirmation` SET `failedAttemp` = `failedAttemp` + 1 WHERE email = ?;";

                $stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);

                }else {
                    //Not ready
                    echo "Error. Please try again.11";
                    die();
                }

                die();
            }else if ($check == true) {
                $sql = "UPDATE `users` SET `confirmation` = 1 WHERE email = ?;";

                $stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);
                }else {
                    //Not ready
                    echo "Error. Please try again.34";
                    die();
                }
                //Deleting the token
                $sql = "DELETE FROM `emailConfirmation` WHERE email = ?;";

                $stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);
                }else {
                    //Not ready
                    echo "Error. Please try again.55";
                    die();
                }
                
                $sql = "SELECT `invitedBy` FROM `users` WHERE email = ?;";
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if ($row = mysqli_fetch_assoc($result)) {
                        //The user is invited by someone
                        //Check if the owner of the invitation code exist
                        $invite = $row['invitedBy'];
                        $sql = "SELECT NULL FROM `users` WHERE `invitation` = \"" . $invite . "\";";
                        $result = mysqli_query($conn, $sql);
                        $result_check = mysqli_num_rows($result);

                        if ($result_check > 0) {
                            //Update the invite count of the user
                            $sql = "UPDATE `users` SET `inviteCount` = `inviteCount` + 1 WHERE `invitation` = \"" . $invite ."\";";
                            mysqli_query($conn, $sql);
                        }
                        
                    }
                }

                echo "Your account has been confirmed. You will be returned to the account page.";
                $_SESSION['confirmation'] = 1;
                mysqli_stmt_close($stmt);

            }
        }
    }else {
        //Not ready
        echo "Error. Please try again.";
        die();
    }

}

?>