<?php
    require_once "globals.inc";
    require_once "lib/ez_sql.php";
    require_once "lib/twishlist-functions.inc";
    
    // get nvps.
    $page       = !empty($_REQUEST['pg']) ? $_REQUEST['pg'] : 1;
    $count = 17;
       
    $pagetitle = "what everyone wants. twishlist.";

    // pass NULL to get all users.
    $wants = getWants(NULL, $page, $count);    

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $pagetitle; ?></title>
<link href="<?php echo STYLES_URI; ?>/reset-fonts-grids.css" rel="styleSheet" type="text/css" />
<link href="<?php echo  STYLES_URI; ?>/twishlist.css" rel="styleSheet" type="text/css" />

<body>
<div id="doc" class="yui-t7">
   <div id="hd">
   <div class="howtouse">
    <ol>    
        <li>Follow <a href="http://www.twitter.com/wants">@wants</a> on twitter</li>
        <li>Send a twitter or <a href="http://twitter.com/direct_messages/create/14438972">direct message</a> to @wants</li>
        <li>Your wants show up here on twishlist!</li>
    </ol>    
   </div>
   <a href="<?php echo ROOT_URI; ?>/index.php"><h1>twishlist</h1><img src="<?php echo IMAGES_URI; ?>/twishlist-logo.png" alt="twishlist" border="0" /></a></div>
   <div id="bd">
    <div class="yui-g">
        <div class="wantslist">
            <h1>Here&rsquo;s what <a href="index.php">everyone</a> wants...</h1>  
    <?php 
    if (count($wants) > 0) {
        $rowcount = 0; // init
        foreach ($wants as $want) {
        
            if ($want->want_user_profile_image_url == "image") {
                $randimage = rand(0,6);
                $want->want_user_profile_image_url = "http://s.twimg.com/a/1263410604/images/default_profile_" . $randimage . "_normal.png";
            }
            
            ?>
            <div class="want <?php echo $rowcount++ % 2 == 0 ? 'alt' : ''; ?>" >
                <div class="date"><?php echo date('M d Y g:ia', strtotime($want->want_created_at)); ?></div>
                <a href="<?php echo userLink($want->want_user_screen_name); ?>"><img src="<?php echo $want->want_user_profile_image_url; ?>" alt="<?php echo $want->want_user_name; ?>" border="0" width="48" /></a>
                <h3><a href="<?php echo userLink($want->want_user_screen_name); ?>"><?php echo $want->want_user_screen_name; ?></a></h3>
                <p><?php echo make_clickable(wantsText($want->want_text)); ?></p>
            </div>
            <?php
        } 

        // page logic
        if ($rowcount == $count) {
            ?><p style="float:right;border:1px solid #7F5BA5;padding:3px;"><a href="<?php echo ROOT_URI; ?>/index.php?pg=<?php echo $page + 1; ?>">Next Page</a></p><?php
        }
        if ($page > 1) {
            ?><p style="float:left;border:1px solid #7F5BA5;padding:3px;"><a href="<?php echo ROOT_URI; ?>/index.php?pg=<?php echo $page - 1; ?>">Prev Page</a></p><?php
        }

    } else {
        ?><p class="error">So Sad! No wants found!</p><?php
    }
    ?>  
    </div>
    </div>
   <div id="ft">Brought to you by the <a href="http://www.yangbus.com/">yangbus</a>.</div>
</div>

<?php /*
<div id="debug"><?php echo $db->debug_queries; ?></div>
*/ ?>
<?php include "includes/google-analytics.inc"; ?>
</body>
</html>
