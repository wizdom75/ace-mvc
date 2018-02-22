<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 03/02/2018
 * Time: 23:07
 */

namespace App;

use App\Models\RememberedLogin;
use \App\Models\User;


/**
 * Class Auth
 * @package App
 *
 * PHP 7.2
 */

class Auth
{
    /**
     * Loin the user method
     *
     * @param User $user The user model
     *
     * @return void
     */
    public static function login($user, $remember_me)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

        if($remember_me){

           if($user->rememberLogin()) {
               setcookie('remember_me', $user->remember_token, $user->expiry_timestamp,'/');
           }

        }
    }

    /**
     * Logout the user
     *
     * @return void
     */
    public static function logout()
    {
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();

        // Forget the remembered login cookie
        static::forgetLogin();

    }

    /**
     * Remember originally requested page in session
     *
     * @return void
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the originally requested page nad redirect to it in login.
     *
     * @return void
     */
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? '/'; // default redirect page
    }

    /**
     * Get the looged in user details from the session or the remember me cookie
     *
     * @return mixed, The user model or NULL if not logged in
     */
    public static function getUser()
    {
        if(isset($_SESSION['user_id'])){

            return User::findByID($_SESSION['user_id']);

        }else{

            return static::loginFromRememberCookie();

        }
    }

    /**
     * Login the user from a remembered login cookie
     *
     * @return mixed The user model inf login cookie found; other wise null
     */
    protected static function loginFromRememberCookie()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if($cookie){

            $remembered_login = RememberedLogin::findByToken($cookie);

            if($remembered_login && !$remembered_login->hasExpired()){

                $user = $remembered_login->getUser();

                static::login($user, false);

                return $user;

            }

        }
    }

    /**
     * Forget the remembered login, if present
     *
     * @return void
     */
    protected static function forgetLogin()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if($cookie){

            $remembered_login = RememberedLogin::findByToken($cookie);

            if($remembered_login){
                $remembered_login->delete();
            }

            setcookie('remember_me', '', time()-3600); // set to expire in the past
        }
    }









}
