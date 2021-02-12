<?php

function random ($conn) {
    
    function randomString($length = 4) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    $random = randomString();

    // require_once "../conn/conn.php";

    $sql = "SELECT NULL FROM `users` WHERE `invitation` = ?";

    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        $check = false;
        
        
        while (!$check) {
            mysqli_stmt_bind_param($stmt, 's', $random);
            
            mysqli_stmt_execute($stmt);
    
            $result = mysqli_stmt_get_result($stmt);
            $result_check = mysqli_num_rows($result);
    
            if ($result_check === 0) {
                $check = true;
                break;
            }else {
                $random = randomString();
            }

        }
        return $random;

    }

}

?>