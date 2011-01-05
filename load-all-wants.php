<?php
    require_once "globals.inc";
    require_once "lib/ez_sql.php";
    require_once "lib/twishlist-functions.inc";
    
    $url = "http://search.twitter.com/search.atom?q=wants&rpp=100"; // summize search
    //$url = "http://localhost/twishlist/summize.atom"; // local test
   

// create a new cURL resource
    $ch = curl_init();
    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // get the response as a string from curl_exec(), rather than echoing it
    curl_setopt($ch, CURLOPT_HEADER, 0);

    // grab URL and set it to $content
    $content = curl_exec($ch);

    // load the XML
    $feed = new SimpleXMLElement($content);

    //echo "Created At: " . $feed->status->created_at . "\n";
    foreach ($feed->children() as $entry) {

        $id = $db->escape(preg_replace("/tag:search.twitter.com,2005:/", "", $entry->id)); 
        $created_at = $db->escape(date('Y-m-d G:i:s',strtotime($entry->published)));
        $text = $db->escape(trim($entry->title)); // use the title, not the linked text
        $user_name = $db->escape(preg_replace("/^(.*) \((.*)\)/","$2",$entry->author->name));
        $user_screen_name = $db->escape(preg_replace("/^(.*) \((.*)\)/","$1",$entry->author->name));
        $user_url = $db->escape($entry->author->uri);
        $user_location = "";
        $user_description = "";        

        // only use if it starts with "wants"
        if (preg_match("/^wants/",$text)) {

        // pain in the ass way to get the image
        foreach ($entry->children() as $entry2) {
            $attributes = $entry2->attributes();
            if ($attributes[0] == "image/png") {
                $user_profile_image_url = $attributes[1];
            }
        }

            if ($id) {

            ?>ID: <?php echo $id; ?>
              user_name: <?php echo $user_name; ?>
              screen: <?php echo $user_screen_name; ?>
              created: <?php $created_at; ?>
              <br />
              text: <?php echo $text; ?>
              <br />
              <?php echo $user_profile_image_url; ?>
              <br />
            <?php

                // insert it into the db -- won't insert if it already exists because of PK
                $sql = "REPLACE INTO wants (want_id, want_created_at, want_text, 
                        want_user_name, want_user_screen_name, want_user_location, 
                        want_user_description, want_user_profile_image_url, want_user_url)
                        VALUES ('$id','$created_at','$text','$user_name','$user_screen_name','$user_location','$user_description',
                                '$user_profile_image_url','$user_url')";

                if ($db->query($sql)) { echo "<b>INSERTED $id </b><hr />\n"; }
            }
        }
    }

    echo "<hr />url: $url <br /><textarea rows='20' cols='120'>" . htmlentities($content) . "</textarea>";

    // close cURL resource, and free up system resources
    curl_close($ch);
    
?>
