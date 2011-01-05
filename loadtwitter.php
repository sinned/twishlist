<?php
    require_once "globals.inc";
    require_once "lib/ez_sql.php";
    require_once "lib/twishlist-functions.inc";

    //$url = "http://twitter.com/statuses/friends_timeline.xml?count=50"; // timeline.
    //$url = "http://twitter.com/direct_messages.xml"; // direct messages
    $url = "http://twitter.com/statuses/replies.xml"; // replies
    echo loadWantsFromTwitter($url) . " wants found.<br />";
    
    $url = "http://twitter.com/direct_messages.xml"; // direct messages
    echo loadWantsFromTwitter($url) . " wants found.<br />";

?>
