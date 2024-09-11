<?php
class User
{
  private $conn;
  public $id;
  public $username;

  public function __call($name, $arguments)
  {
    //print($name);
    $property = preg_replace("/[^0-9a-zA-Z]/", "", substr($name, 3));
    $property = strtolower(preg_replace('/\B([A-Z])/', '_$1', $property));
    //echo $property; //ForChecking Purpose
    if (substr($name, 0, 3) == "get") {
      return $this->_get_data($property);
    } elseif (substr($name, 0, 3) == "set") {
      return $this->_set_data($property, $arguments[0]);
    } else {
      //This else part more heplfull for finding error When using __call function, because __call function just forward the msg Even if it's not available in the database.
      throw new Exception("User::__call -> $name,function unavailable.");
    }
    //print("User::__call -> $name");
  }

  //this function helps to retrieve data from the database
  private function _get_data($var)
  {
    if (!$this->conn) {
      $this->conn = Database::getConnection();
    }
    //$sql = "SELECT `$var` FROM `users` WHERE `id` = $this->id";
    $sql = "SELECT `$var` FROM `Auth` WHERE `id` = $this->id";
    $result = $this->conn->query($sql);
    if ($result and $result->num_rows == 1) {
      //print("Res: " . $result->fetch_assoc()["$var"]);
      return $result->fetch_assoc()["$var"];
    } else {
      return null;
    }
  }

  //this function helps to set the data from the database
  private function _set_data($var, $data)
  {
    if (!$this->conn) {
      $this->conn = Database::getConnection();
    }
    //$sql = "UPDATE `users` SET `$var`='$data' WHERE `id` = $this->id;";
    $sql = "SELECT `$var` FROM `Auth` WHERE `id` = $this->id";
    if ($this->conn->query($sql)) {
      return true;
    } else {
      return false;
    }
  }

  //private static $salt = "dsfhsefjpjdsfosd";
  public static function signup($user, $phone, $email, $pass)
  {
    //$pass = md5(strrev(md5($pass)) . User::$salt); // Security through obscurity
    $option = [
      'cost' => 8,
    ];
    $pass = password_hash($pass, PASSWORD_BCRYPT, $option);
    $conn = Database::getConnection();
    $sql = "INSERT INTO `Auth` (`username`, `password`, `email`, `phone`, `blocked`, `active`)
    VALUES ('$user', '$pass', '$email', '$phone', '0', '1');";
    // $error = false;
    $result = $conn->query($sql);
    if ($result) {
      $error = false;
    } else {
      // echo "Error:" . $sql . "<br>" . $conn->error;
      $error =  $conn->error;
    }
    //$conn->close();
    return $error;
  }

  public static function login($user, $pass)
  {
    $conn = Database::getConnection();
    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM `Auth` WHERE `username` = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    // $query = "SELECT * FROM `Auth` WHERE `username` = '$user'";  //Genaral query statements
    // $result = $conn->query($query);
    // Get the result
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      if (password_verify($pass, $row['password'])) {
        /*
        1. Generate Session Token
        2. Insert Session Token
        3. Build session and give session to user.
        */
        return $row['username']; //username
      } else {
        // Incorrect password
        return false;
      }
    } else {
      // User not found
      return false;
    }
    // Close the statement
    $stmt->close();
  }

  //user object can be constructed with both UserID and Username.
  public function __construct($username)
  {
    $this->conn = Database::getConnection();
    $this->username = $username;
    $this->id = null;
    $sql = "SELECT `id` FROM `Auth` WHERE `username`='$username' OR `id`='$username' LIMIT 1"; //OR `id`='$username'
    $result = $this->conn->query($sql);
    if ($result->num_rows) {
      $row = $result->fetch_assoc();
      $this->id = $row['id']; //updating from the database
    } else {
      throw new Exception("Username doesn't exist");
    }
    //print($this->id);
  }


  // public function getUsername()
  // {
  //   return $this->username;
  // }
}

// public function setBio($bio)
// {
//TODO: Write UPDATE command to change new bio
//   return $this->_set_data('bio', $bio);
// }
// public function getBio()
// {
//TODO: Write SELECT command to get the bio
//   return $this->_get_data('bio');
// }

// public function setAvatar($link)
// {
//   return $this->_set_data('avatar', $link);
// }
// public function getAvatar()
// {
//   return $this->_get_data('avatar');
// }

// public function setLastname()
// {
//   return $this->_set_data('lastname', $name);
// }
// public function getLastname()
// {
//   return $this->_get_data('lastname');
// }

// public function setDob($year, $month, $day)
// {
//   if (checkdate($month, $day, $month)) {
//     return $this->_set_data('dob', "$year.$month.$day");
//   } else 
//   {
//     return false;
//   }
// }
// public function getDob(){
//   return $this->_get_data('dob');
// }

// public function setInstagramlink($link)
// {
//   return $this->_set_data('instagram', $link);
// }
// public function getInstagramlink()
// {
//   return $this->_get_data('instagram');
// }

// public function setTwitterlink($link)
// {
//   return $this->_set_data('twitter', $link);
// }
// public function getTwitterlink()
// {
//   return $this->_get_data('twitter');
// } 

// public function setFacebooklink($link)
// {
//   return $this->_set_data('facebook', $link);
// }
// public function getFacebooklink()
// {
//   return $this->_get_data('facebook');
// }
