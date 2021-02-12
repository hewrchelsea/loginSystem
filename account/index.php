<?php

session_start();
$inviteCount;
$earlyAccess;
/*foreach ($_SESSION as $session_name => $value) {
    echo $session_name . ": " . $value . "<br><br>";
}*/


if (isset($_SESSION['uid'])) {

    if ($_SESSION['confirmation'] != 1) {
        header("Location: ../confirmEmail") or die();
    }
    
    require "../conn/conn.php";
    
    $uid = $_SESSION['uid'];
    
    $sql = "SELECT `inviteCount`, `earlyAccess` FROM `users` WHERE `uid`='" . $uid . "';";

    $result = mysqli_query($conn, $sql);
    $result_check = mysqli_num_rows($result);

    if ($result_check > 0) {
        //Expected
        if ($row = mysqli_fetch_assoc($result)) {
            $inviteCount = $row['inviteCount'];
            $earlyAccess = $row['earlyAccess'];
        }
    }

}else {
    //Return them to the login page
    header("Location: ../login");
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <?php
            if ($inviteCount >= 3 || $earlyAccess == 1) {
                ?>
                
                    <h1 class="title">You are good to go!</h1>
                    <p class="description">Download the app from the link below</p>
                    <div class="a-parent">
                        <a href="https://chrome.google.com/webstore/detail/dark-mode/dmghijelimhndkbmpgbldicpogfkceaj">https://chrome.google.com/webstore/detail/dark-mode/dmghijelimhndkbmpgbldicpogfkceaj</a>
                    </div>

                <?php
            }else {

                $links = new StdClass;

                $links->fb = "https://www.facebook.com/sharer/sharer.php?u=http://127.0.0.1:81/signup/?invitation=" . $_SESSION['invitation'];
                $links->twitter = "https://twitter.com/intent/tweet?url=http://127.0.0.1:81/signup/?invitation=" . $_SESSION['invitation'] . "&text=Create an account from this link, to download a free Supreme bot. #Botolic";

                $links->mail = "mailto:info@example.com?&subject=&body=Create an account from this link, to download a free Supreme bot. http://127.0.0.1:81/signup/?invitation=" . $_SESSION['invitation'];
                $links->clipboard = "http://127.0.0.1:81/signup/?invitation=" . $_SESSION['invitation'];


                ?>
                    <h1 class="title">Almost there!</h1>
                    <p class="description">Invite Three, or more friends to unlock the app. </p>
                    <div class="a-parent">
                        <a href="http://127.0.0.1:81/signup/?invitation=<?php echo $_SESSION['invitation'];?>">
                            http://127.0.0.1:81/signup/?invitation=<?php echo $_SESSION['invitation'];?>
                        </a>
                    </div>
                    <div class="shareBtn">
                    </div>

                    <div class="links">
                        <a target="_blank" href="<?php echo $links->fb; ?>" class="fb"></a>
                        <a target="_blank" href="<?php echo $links->twitter; ?>" class="twitter"></a>
                        <a target="_blank" href="<?php echo $links->mail; ?>" class="mail"></a>
                        <a href="javascript:void(0)" class="clipboard"></a>
                    </div>
                <?php
            }
        ?>
    </main>
</body>
<script src="main.js"></script>
</html>