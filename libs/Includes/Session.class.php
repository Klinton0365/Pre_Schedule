<?php  //These are the TOP Three SESSION Function 
class Session
{
    public static $isError = false;
    public static $user = null;
    public static $usersession = null;

    public static function start()
    {
        session_start();
    }
    public static function unset_all()
    {
        session_unset();
    }
    public static function destroy()
    {
        session_destroy();
        /*
        If UserSession is active, set it to inactive.
        */
    }
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value; //$_SESSION['Session_token'] = $value;  //$value == $token
    }
    public static function delete($key)
    {
        unset($_SESSION[$key]);
    }

    public static function isset($key)
    {
        //var_dump($_SESSION[$key]);
        return isset($_SESSION[$key]);
    }

    public static function get($key, $default = false)
    {
        if (Session::isset($key)) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    public static function getUserSession()
    {
        //return Session::get('user_session'); //$_SESSION[user_session]
        return Session::$usersession;
    }

    public static function getUser()
    {
        return Session::$user;
    }

    public static function loadTemplate($name)
    {
        $script = $_SERVER['DOCUMENT_ROOT'] . get_config('base_path') . "_templates/$name.php";
        //$script = $_SERVER['DOCUMENT_ROOT'] . "/htdocs/Photogram/htdocs/_template/$name.php"; //. get_config('base_path') = "base_path": "/Photogram/"
        if (is_file($script)) {
            include $script;
        } else {
            Session::loadTemplate('_error');
        }
    }

    public static function renderPage()
    {
        Session::loadTemplate('_master'); //This methos is called wrapper function
    }

    public static function currentScript()
    {
        return basename($_SERVER['SCRIPT_NAME'], '.php');
    }

    // public static function isAuthenticated() //mycode
    // {
    //     //TODO: Is a correct implementaion?
    //     if (is_object(Session::getUserSession())) {
    //         return Session::getUserSession()->isvalid();
    //     } else {
    //         return false;
    //     }
    //     //return Session::getUserSession()->isValid();
    // }
    public static function isAuthenticated()
    {
        //TODO: Is it a correct implementation?
        if (is_object(Session::getUserSession())) {
            return Session::getUserSession()->isValid();
            // Session::set('_Redirect', Session::currentScript());
        } else {
            return false;
        }
    }

    public static function ensureLogin()
    {
        if (!Session::isAuthenticated()) {
            header("Location: /login.php");
            die();
        }
    }
}
