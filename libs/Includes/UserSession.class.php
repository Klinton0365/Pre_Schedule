<?php
//use function PHPSTORM_META\sql_injection_subst;
class UserSession
{
  public $data;
  public $id;
  public $conn;
  public $token;
  public $uid;
  /*
 *This function will return a session ID if username and password is correct.
 *@return SessionID
 */

  public function __construct($token)
  {
    $this->conn = Database::getConnection();
    $this->token = $token;
    $this->data = null;
    $sql = "SELECT * FROM `session` WHERE `token` = '$token' LIMIT 1";
    $result = $this->conn->query($sql);
    if ($result->num_rows) {
      $row = $result->fetch_assoc();
      $this->data = $row;
      $this->uid = $row['uid'];
    } else {
      //header('Location: index.php');
      throw new Exception("session invalid");
    }
  }

  public static function authenticate($user, $pass)
  {
    $fingerprint = null;
    // if ($fingerprint == null) {
    //   $fingerprint = $_COOKIE['fingerprint'];
    // }
    $username = User::login($user, $pass);
    if ($username and isset($_COOKIE['fingerprint'])) { //This condition creates PHP session for logged USER and store the USERNAME
      $user = new User($username);
      $conn = Database::getConnection();
      $ip = $_SERVER['REMOTE_ADDR'];
      $agent = $_SERVER['HTTP_USER_AGENT'];
      $fingerprint = $_COOKIE['fingerprint'];
      $token = md5(rand(0, 9999999) . $ip . $agent . time());
      $sql = "INSERT INTO `session` (`uid`, `token`, `login_time`, `ip`, `user_agent`, `active`,`fingerprint`) 
      VALUES ('$user->id', '$token', now(), '$ip', '$agent', '1','$fingerprint')";
      if ($conn->query($sql)) {
        Session::set('session_token', $token);
        Session::set('fingerprint', $fingerprint);
        //Session::set('is_loggedin', true);
        //This set function store USERNAME to the Session and it'll show the USERNAME to a Home Webpage of WEB.
        Session::set('session_username', $username);
        return $token;  // Return the session token
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public static function authorize($token)  //This authorize will work when pages loads everytime. Basically everytime authorize a user when pages loads.
  {
    try {
      $session = new UserSession($token);

      // Additional checks for session validity
      if (isset($_SERVER['REMOTE_ADDR']) and isset($_SERVER["HTTP_USER_AGENT"])) {
        //echo "HTTP_USER_AGENT and REMOTE_ADDR both values are set";
        if ($session->isValid() and $session->isActive()) {
          //echo "isValid() and isActive() are working perfectly";
          if ($_SERVER['REMOTE_ADDR'] == $session->getIP()) {
            //echo "Database IP and currently using browser IP are the same";
            if ($_SERVER['HTTP_USER_AGENT'] == $session->getUserAgent()) {
              //echo "Database Browser and currently using browser are the same";
              if ($session->getFingerprint() == $_COOKIE['fingerprint']) { //TODO: This is always true, fix it
                //echo "Fingeprint is same";
                Session::$user = $session->getUser();
                return $session;
              } else throw new Exception("FingerPrint doesn't match");
            } else throw new Exception("User agent does't match");
          } else throw new Exception("IP does't match");
        } else {
          $session->removeSession();
          Session::destroy();
          // Redirect to index page after removing session
          header('Location: index.php');
          exit();
        }
      } else throw new Exception("IP and User_agent is null");
    } catch (Exception $e) {
      error_log('Something is wrong: ' . $e->getMessage());
      // Handle the exception or rethrow it for further debugging
      throw $e;
    }
  }


  /*
   * Returns a User object associated with the session's user ID.
   * @return User object
   */
  public function getUser()
  {
    return new User($this->uid);
  }

  /*
   *Check if the validity of the session is within one hour, else it inactive
   *@return boolean
   */

  /*
   * Check if the session fingerprint is set.
   * @return boolean
   */
  public function getFingerPrint()
  {
    if (isset($this->data['fingerprint'])) {
      return $this->data['fingerprint'] ? true : false;
    }
  }
  /*
   * Check if the session is active.
   * @return boolean
   */
  public function isActive()
  {
    if (isset($this->data['active'])) {
      return $this->data['active'] ? true : false;
    }
  }

  /*
   * Check if the session is valid within one hour.
   * @return boolean
   */
  public function isValid()
  {
    if (isset($this->data['login_time'])) {
      $login_time = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['login_time']);
      //if (30 > time() - $login_time->getTimeStamp()) {
      if (3600 > (time() - $login_time->getTimeStamp())) {
        //var_dump(3600 > time() - $login_time->getTimeStamp());
        return true;
      } else {
        return false;
      }
    } else {
      //header('Location: index.php');
      throw new Exception("Login time is null");
    }
  }



  /*
   * Get the session IP address.
   * @return IP address or false if not set
   */
  public function getIP()
  {
    return isset($this->data["ip"]) ? $this->data["ip"] : false;
  }
  /*
   * Get the session user agent.
   * @return User agent or false if not set
   */
  // public function getUserAgent()
  // {
  //   return isset($this->data["useragent"]) ? $this->data["useragent"] : false;
  // }
  public function getUserAgent()
  {
    return isset($this->data["user_agent"]) ? $this->data["user_agent"] : false;
  }

  /*
   * Remove the current session from the database.
   * @return true on successful removal,
   */
  public function removeSession()
  {
    if (isset($this->data['id'])) {
      $id = $this->data['id'];
      if (!$this->conn) {
        $this->conn = Database::getConnection();
      }
      $sql = "DELETE FROM `session` WHERE `id` = $id;";
      if ($this->conn->query($sql)) {
        return true;
      } else {
        return true;
      }
    }
  }
}
