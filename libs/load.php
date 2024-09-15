<?php
// include_once 'Includes/Mic.class.php';
include_once 'Includes/Session.class.php';
include_once 'Includes/User.class.php';
include_once 'Includes/Database.class.php';
include_once 'Includes/UserSession.class.php';
include_once 'Includes/WebAPI.class.php';

// global $__site_config;


$wapi = new WebAPI();
$wapi->initiate_Session();

//This file for reading database credentials And once this readed the info, it stored the credential into the cache so don't wanna to open read again and again to this file.
// function get_config($key, $default = null)
// {
//     global $__site_config;
//     $array = json_decode($__site_config, true);
//     if (isset($array[$key])) {
//         return $array[$key];
//     } else {
//         return $default;
//     }
// }

// function load_template($name)
// {
//     include $_SERVER['DOCUMENT_ROOT'] . "/_template/$name.php";
// }


// function validate_credential($email_address, $password)
// {
//     if ($email_address == "klinton@gmail.com" and $password == "password") {
//         return true;
//     } else {
//         return false;
//     }
// }
