<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 01/02/2018
 * Time: 23:03
 */

namespace App\Controllers;

use \App\Models\User;
use \Core\View;

/**
 * Class Signup
 * @package App\Controllers
 *  Signup Controller
 *
 *  PHP 7.2
 */
class Signup extends \Core\Controller
{
    /**
     *  Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }
    /**
     *  Sign up new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);

        if($user->save()){

            $user->sendActivationEmail();

            $this->redirect('/ace-mvc/public/signup/success');
        }else{
            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);
        }
    }
    /**
     *  Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }

    /**
     *   Activate a new account
     *
     *  @return void
     */
    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect('/ace-mvc/public/signup/activated');
    }

    /**
     * Show the activation success page
     *
     * @return void
     */
    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }


}