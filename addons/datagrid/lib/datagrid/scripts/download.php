<?php

    $dir = isset($_GET['dir']) ? $_GET['dir'] : "";
    $file = isset($_GET['file']) ? $_GET['file'] : "";
    
    $file_path = "../../".$dir.$file;
    
    if (file_exists($file_path) && (strlen($file) == 10) && (substr($file, 0, 6) == "export")) {
        // strlen() added for security reasons
        header("Content-type: application/force-download"); 
        header('Content-Disposition: inline; filename="'.$file.'"'); 
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-length: ".filesize($file_path)); 
        header('Content-Type: application/octet-stream'); 
        header('Content-Disposition: attachment; filename="'.$file.'"'); 
        readfile($file_path);
    } else { 
        echo "Can not find such path: $file_path !"; 
    }
    exit(0);

?>