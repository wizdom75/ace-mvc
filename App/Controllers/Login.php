<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 02/02/2018
 * Time: 23:22
 */

namespace App\Controllers;

use App\Flash;
use \Core\View;
use \App\Models\User;
use \App\Auth;

/**
 * Class Login
 * @package App\Controllers
 *
 *  PHP version 7.2
 */
class Login extends \Core\Controller
{
    /**
     *  Show the login page
     *
     *  @return void
     */
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    /**
     * Login a user
     *
     * @return void
     */
    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);

        $remember_me = isset($_POST['remember_me']);

        if($user){
            Auth::login($user, $remember_me);



            Flash::addMessage('Login successful');

            $this->redirect(Auth::getReturnToPage());

        }else{

            Flash::addMessage('Login unsuccessful, please try again.', Flash::WARNING);

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me

            ]);
        }
    }

    /**
     *  Log out a user
     *
     * @return void
     */
    public function destroyAction()
    {
       Auth::logout();

        $this->redirect('/ace-mvc/public/login/show-logout-message');

    }

    /**
     * Show a logged out flash message amd redirect to the homepage. Necessary to use flashe messages
     * as they use the session and at the end og the logout method (destroyAction) all sessions destroyed
     * so new action needs to be called in order to use the session.
     *
     * @return void
     */
    public function showLogoutMessageAction()
    {
        Flash::addMessage('Logout Successful');

        $this->redirect('/ace-mvc/public');
    }

}