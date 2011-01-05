<?php
define('TWITTERHANDLE', 'twitterhandle');
define('TWITTERPASSWORD', 'twitterpassword');

$glbl_rootUri = "";
define('APP_SHORT_NAME', 'twishlist');


// figure out what environment and which domain we're on
if (function_exists("posix_uname")) {
    $environment = "PRODUCTION";
}

// not sure where we are, so assume a dev machine
if (!isset($environment)) $environment = "DEVELOPMENT";

switch ($environment) {
    // settings for production
    case "PRODUCTION":

        // database settings
        $glbl_dbname = "database_name";            // default db name
        $glbl_dbuser = "database_username";            // default db username
        $glbl_dbpass = "database_password";            // default db password
        $glbl_dbhost = "database_host";  // default db host
        break;
        
    // settings for personal development environments
    case "DEVELOPMENT":
    default:
        // database settings
        $glbl_dbname = "twishlist";            // default db name
        $glbl_dbuser = "twishlist";            // default db username
        $glbl_dbpass = "twishlist";            // default db password
        $glbl_dbhost = "localhost";  // default db host    
        
        $glbl_rootUri = preg_replace("@/(" . APP_SHORT_NAME . ")/.*$@i", "/$1", $_SERVER['REQUEST_URI']);        
        break;
}

// figure out the base URL, if we can
if (isset($_SERVER['REQUEST_URI'])) {
    $glbl_baseUrl = "http://" . $_SERVER['HTTP_HOST'];
}

/* Base URL */
define('BASE_URL', $glbl_baseUrl);

/* URIs */
define('ROOT_URI', $glbl_rootUri);
define('APP_ROOT', $glbl_rootUri);
define('STYLES_URI',  APP_ROOT . '/styles');
define('SCRIPTS_URI', APP_ROOT . '/scripts');
define('IMAGES_URI',  APP_ROOT . '/images');

// db connection constants
define('EZSQL_DB_NAME', $glbl_dbname);
define('EZSQL_DB_USER', $glbl_dbuser);
define('EZSQL_DB_PASSWORD', $glbl_dbpass);
define('EZSQL_DB_HOST', $glbl_dbhost);

?>
