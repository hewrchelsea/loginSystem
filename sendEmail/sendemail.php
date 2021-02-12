<?php

function sendEmail ($from, $to, $subject, $html) {
    $email = "YourEmail@example.com"; /*change this*/

    if (!empty($from) && !empty($to) && !empty($subject) && !empty($html)) {



        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <'.$from.'>' . "\r\n";
        if (mail($to, $subject, $html, $headers)) {
            
        }else{
            //Failed to send message
            echo "Failed to sned email!";
        }
    }else {
        //One of the informations is missing
        //Email not sendt
        echo "Failed to send email!";
    }
}
?>