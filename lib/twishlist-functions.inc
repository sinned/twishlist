<?php
// twishlist functions

function getWants($username=NULL, $page=1, $count=20) {

    global $db;
    
    // clean up passed in vars
    $username = $db->escape($username);
    $page = $db->escape($page);
    $count = $db->escape($count);
    
    $select_sql = "SELECT want_id, want_created_at, want_text, 
            want_user_name, want_user_screen_name, want_user_location, 
            want_user_description, want_user_profile_image_url, want_user_url
            FROM wants ";
            
    if ($username) {
        $where_sql = " WHERE want_user_screen_name = '$username' AND want_hide = '0' ";
    } else {
        $where_sql = " WHERE want_hide = '0' ";
    }
            
    $orderby_sql = " ORDER BY want_created_at DESC ";
    
    $start = ($page-1) * $count;
    
    $limit_sql = " LIMIT $start, $count ";
    
    $sql = $select_sql . $where_sql . $orderby_sql . $limit_sql;

    return $db->get_results($sql);

} // end-function getWants

function loadWantsFromTwitter($url) {

    global $db;

    // create a new cURL resource
    $ch = curl_init();
    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // get the response as a string from curl_exec(), rather than echoing it
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERPWD,TWITTERHANDLE . ":" . TWITTERPASSWORD);

    // grab URL and set it to $content
    $content = curl_exec($ch);

    // load the XML
    $feed = new SimpleXMLElement($content);

    //echo "Created At: " . $feed->status->created_at . "\n";
    foreach ($feed->children() as $status) {

        $id = $db->escape($status->id);
        $created_at = $db->escape(date('Y-m-d G:i:s',strtotime($status->created_at)));
        $text = $db->escape($status->text);
        
        if (isset($status->user)) {
            $user_name = $db->escape($status->user->name);
            $user_screen_name = $db->escape($status->user->screen_name);
            $user_location = $db->escape($status->user->location);
            $user_description = $db->escape($status->user->description);
            $user_profile_image_url = $db->escape($status->user->profile_image_url);
            $user_url = $db->escape($status->user->url);
        } elseif (isset($status->sender)) {
            $user_name = $db->escape($status->sender->name);
            $user_screen_name = $db->escape($status->sender->screen_name);
            $user_location = $db->escape($status->sender->location);
            $user_description = $db->escape($status->sender->description);
            $user_profile_image_url = $db->escape($status->sender->profile_image_url);
            $user_url = $db->escape($status->sender->url);        
        } else {
            $user_name = "";
            $user_screen_name = "";
            $user_location = "";
            $user_description = "";
            $user_profile_image_url = "";
            $user_url = "";        
        }
        
        if ($id) {
            // insert it into the db -- won't insert if it already exists because of PK
            $sql = "REPLACE INTO wants (want_id, want_created_at, want_text, 
                    want_user_name, want_user_screen_name, want_user_location, 
                    want_user_description, want_user_profile_image_url, want_user_url)
                    VALUES ('$id','$created_at','$text','$user_name','$user_screen_name','$user_location','$user_description',
                            '$user_profile_image_url','$user_url')";

            if ($db->query($sql)) { echo "INSERTED $id <br />\n"; }
        }
    }

    echo "<hr />url: $url <br /><textarea rows='20' cols='90'>" . htmlentities($content) . "</textarea>";

    // close cURL resource, and free up system resources
    curl_close($ch);
    
    return count($feed->children());

} // end-function loadWantsFromTwitter

/**
 * Return a clickable version of a link.
 *
 * @param string $ret
 * @return string
 */
function make_clickable($ret) {
    $ret = ' ' . $ret . ' ';
    $ret = preg_replace("#([\s>])(https?)://([^\s<>{}()]+[^\s.,<>{}()])#i", "$1<a href='$2://$3' rel='nofollow'>$2://$3</a>", $ret);
    $ret = preg_replace("#(\s)www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^ <>{}()\n\r]*[^., <>{}()\n\r]?)?)#i", "$1<a href='http://www.$2.$3$4' rel='nofollow'>www.$2.$3$4</a>", $ret);
    $ret = preg_replace("#(\s)([a-z0-9\-_.]+)@([a-z0-9\-_.]+)\.([^,< \n\r]+)#i", "$1<a href=\"mailto:$2@$3.$4\">$2@$3.$4</a>", $ret);
    $ret = trim($ret);
    return $ret;
}

// gets rid of @wants from the text
function wantsText($ret) {
    //$ret = preg_replace("/^wants/", "", $ret); // get rid of first wants
    //$ret = preg_replace("/^@wants/", "", $ret);
    //$ret = preg_replace("/^@ wants/", "", $ret);
    
    return $ret;
}

// changes "normal" into "bigger" (for the twitter profile pics)
function makeBigger($ret) {
    $ret = preg_replace("/normal/", "bigger", $ret);
    return $ret;
}

// builds user link based on environment
function userLink($username) {
    global $environment;
    
    switch ($environment) {
        case "PRODUCTION":
            $link = "wants/$username/";
            break;
        case "DEVELOPMENT":
        default:
            $link = "user.php?u=$username";
            break;
    }
    
    return $link;
}

?>
