<?php
session_start();


if (isset($_SESSION['uid']))
    header('Location: ../account') or die();
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
            <header class="website-header">
                <a href="/" class="logo">
                    LOGO
                </a>

                <nav class="website-navi">
                    <a href="/">Home</a>
                    <a href="/account">Account</a>
                    <a href="/#download/">Buy now</a>
                    <a href="/contact/" class="active">Contact</a>
                </nav>
                <div class="burger">
                    <div class="a"></div>
                    <div class="a"></div>
                    <div class="a"></div>
                </div>
                <div class="closeBurger">×</div>
            </header>
        <div class="burgerDropdown">
            <section>
                <div class="item">
                    <h1 class="title">Navigation</h1>
                    <a href="/">Home</a>
                    <a href="/account">Account</a>
                    <a href="/#download/">buy now</a>
                    <a href="/contact">contact</a>
                </div>
                <div class="item">
                    <h1 class="title">Other links</h1>
                    <a href="/help">Help</a>
                    <a href="/news">News/blog</a>
                    <a href="/privacy policy">privacy policy</a>
                    <a href="/terms and conditions">terms & conditions</a>
                </div>
                <p class="copy">© 2020. ALL RIGHTS RESERVED.</p>
            </section>
        </div>

        <section class="login-section">
            <h1 class="title">Login</h1>

            <form action="submit.php" method="POST" class="login-form">
                <div class="input">
                    <h2 class="input-title">Email or username</h2>
                    <input type="text" name="uid" class="uid">
                </div>
                <div class="input">
                    <h2 class="input-title">Password</h2>
                    <input type="password" name="pwd" class="pwd">
                </div>

                <button type="submit">Login</button>
            </form>
            <p class="msg"></p>
            <p class="loginTxt"><a href="/forgot-password" class="real-link loginLink">Forgot password? </a></p>
            <p class="loginTxt">Don't have an account? <a href="/signup" class="real-link loginLink">Signup</a></p>
        </section>
    </div>
</body>
<script src="main.js"></script>
<script src="../js/_header.js"></script>

</html>