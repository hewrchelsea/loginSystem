<?php
session_start();


if (isset($_SESSION['uid'])) {
    http_response_code(404);
    die();
}
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="icon/png" href="../images/icons/128.png">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
    <script src="../js/jquery.js"></script>
</head>

<body>

    <div class="pageOne">

        <section class="login-section">

        <?php
        
        if (isset($_GET['selector']) && isset($_GET['validator'])) {
            
            $selector = $_GET['selector'];
            $validator = $_GET['validator'];

            if (!empty($selector) && !empty($validator)) {
                if (ctype_xdigit($selector) === true && ctype_xdigit($validator) === true) {

                    ?>

                    <h1 class="title">Password reset</h1>

                    <form action="submit.php" method="POST" class="login-form">

                        <input type="hidden" name="selector" class="selector" value="<?php echo $selector;?>">
                        <input type="hidden" name="validator" class="validator" value="<?php echo $validator;?>">
                        <div class="input">
                            <h2 class="input-title">Password</h2>
                            <input type="password" name="pwd" class="pwd">
                        </div>
                        <div class="input">
                            <h2 class="input-title">Confirm password</h2>
                            <input type="password" name="pwdConfirm" class="pwdConfirm">
                        </div>

                        <button type="submit">Login</button>
                    </form>
                    <p class="msg"></p>
                    
                    
                    <?php

                }else {

                }
            }else {
                //Error
                echo "Error";
            }
        }else {
            echo "Error";
        }

        
        ?>
        </section>
    </div>
</body>
<script src="createNew.js"></script>
<script src="../js/_header.js"></script>

</html>