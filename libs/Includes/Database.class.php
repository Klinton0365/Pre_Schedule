<?php
class Database
{
 public static $conn = null;
 public static function getConnection()
 {
  if (Database::$conn == null) {
   //try {
   $servername = "mysql.selfmade.ninja";//get_config('db_server');
   $username = "Klinton_03";//get_config('db_username');
   $password = "Klinton@432305";//get_config('db_password');
   $dbname =  "Klinton_03_presched";//get_config('db_name');

   // Create connection
   $connection = new mysqli($servername, $username, $password, $dbname);
   // Check Connection
   if ($connection->connect_error) {
    die("Connecton Failed: " . $connection->connect_error);
   } else {
    //print("New connection Establishing...");
    Database::$conn = $connection; // Replacing null with actual connection
    return Database::$conn;
   }
   // } catch (Exception $e) {
   //          echo 'Caught exception: ', $e->getMessage(), "\n";
   // }
  } else {
   //print("Returning Existing Establishing...");
   return Database::$conn;
  }
 }
}
