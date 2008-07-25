<?php

    $dir = isset($_GET['dir']) ? $_GET['dir'] : "";
    $file = isset($_GET['file']) ? $_GET['file'] : "";
    
    $file_path = realpath($_SERVER['DOCUMENT_ROOT']).'/'.$dir.$file;
    
    if (file_exists($file_path) && (strlen($file) == 10) && (substr($file, 0, 6) == "export")) {
        // strlen() added for security reasons
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header ("Pragma: no-cache"); // HTTP/1.0
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