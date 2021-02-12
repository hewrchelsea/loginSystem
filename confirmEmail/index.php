<?php
session_start();




if (!isset($_SESSION['uid']))
    header("Location: ../login") or die();

if ($_SESSION['confirmation'] == 1) {
    http_response_code(404);
    die();
}    


$sql = "SELECT * FROM `emailConfirmation` WHERE `email`=?";

require_once "../conn/conn.php";

$stmt = mysqli_stmt_init($conn);

if (mysqli_stmt_prepare($stmt, $sql)) {

    $email = $_SESSION['email'];
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$row = mysqli_fetch_assoc($result)) {
        //There is no token
        //Send a token
        //generate a six number code
        $token = mt_rand(100000, 999999);
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);
        $expires =  date("U") + 1800;
        $failedAttemp = 0;


        $sql = "INSERT INTO `emailConfirmation` (`email`, `code`, `expires`, `failedAttemp`) VALUES(?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssii", $email, $tokenHash, $expires, $failedAttemp);
            mysqli_stmt_execute($stmt);

            //Send the email
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


        }else {
            //Not ready
            echo "Error. Please reload the page.";
            die();
        }

    }

}else {
    //Not ready
    echo "Error. Please try again later.";
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="style.css">
    <script src="../js/jquery.js"></script>
</head>
<body>
    <main>
                
        <h1 class="title">You have to confirm your email</h1>
        <p class="description">We have sent a confirmation code, to your email. If you didn't recieve one, <a class="real-link" href="javascript:void(0)">request a new one</a></p>
        <form action="submit.php" method="POST">
            <div class="input">
                <p class="input-title">Confirmation code</p>
                <input type="tel" class="code">
            </div>
            <button class="submit">Submit</button>
        </form>
        <p class="msg"></p>
    </main>
</body>
<script src="main.js"></script>
</html>




<?php


/*
session_unset();
session_destroy();
*/