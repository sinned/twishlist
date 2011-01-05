<?php   
    $url = "http://preview.tinyurl.com/8sfveo"; // summize search   

// create a new cURL resource
    $ch = curl_init();
    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // get the response as a string from curl_exec(), rather than echoing it
    curl_setopt($ch, CURLOPT_HEADER, 0);

    // grab URL and set it to $buffer
    $buffer = curl_exec($ch);
    
    // close cURL resource, and free up system resources
    curl_close($ch);
    
    // get the <blockquote> part
    $blockquote = preg_match("/(<blockquote><b>)(.*)(<br \/><\/b><\/blockquote)/", $buffer, $matches);
    
    echo $matches[2];
    
?>
