<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
NOTE: Loacation of configuration
in Lab: /home/user/Photogramconfig.json
in server: /var/www/photogramconfig.json
*/

class WebAPI
{
  public function __construct()
  {
    // if (php_sapi_name() == "cli") {
    //  global $__site_config;
    //  $__site_config_path = '/home/klinton.developer365/htdocs/Photogram/Project/photogramconfig.json';
    //  $__site_config = file_get_contents($__site_config_path);
    //  //print($__site_config);
    // } else if (php_sapi_name() == "apache2handler") {
    //  if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/Photogram/Project/photogramconfig.json')) {
    //   die('Config file not found!');
    //  }

    // global $__site_config;
    // //$configPath = $_SERVER['DOCUMENT_ROOT'] . "/Photogram/Project/photogramconfig.json"; // /home/klinton.developer365/htdocs/Photogram/Project/photogramconfig.json
    // //print(__DIR__);
    // $__site_config_path = __DIR__ . '/../../../conf/photogramconfig.json';
    // $__site_config = file_get_contents($__site_config_path);

    // if ($__site_config === false) {
    //   die('Error reading config file: ' . error_get_last()['message']);
    // }

    Database::getConnection();
  }
  public function initiate_Session()
  {
    Session::start();
    // if (Session::isset("session_token")) {
    //   try {
    //     Session::$usersession = UserSession::authorize(Session::get('session_token'));
    //   } catch (Exception $e) {
    //     error_log('Error authorizing user session: ' . $e->getMessage());
    //     // Handle the exception or rethrow it for further debugging
    //     //throw $e;

    //     // Redirect to the root directory
    //     header("Location: /"); // Adjust the location if needed
    //     //exit();
    //     throw $e;
    //   }
    // }
  }
}
