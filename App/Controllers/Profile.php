<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 11/02/2018
 * Time: 18:16
 */

namespace App\Controllers;

use App\Auth;
use App\Flash;
use Core\View;

/**
 * Class Profile
 * @package App\Controllers
 *
 * PHP version 7.2
 */

class Profile extends Authenticated
{

    /**
     * Before filter - called before each action method
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * Show profile
     *
     * @return void
     */
    public function showAction()
    {
        View::renderTemplate('Profile/show.html', [
            'user'=> $this->user
        ]);
    }

    /**
     * Show the form for editing the profile
     *
     * @return void
     */
    public function editAction()
    {
        View::renderTemplate('Profile/edit.html', [
            'user'=> $this->user
        ]);
    }

    /**
     * Update profile
     *
     * @return void
     */
    public function updateAction()
    {

        if($this->user->updateProfile($_POST)){

            Flash::addMessage('Changes to profile saved.');

            $this->redirect('/ace-mvc/public/profile/show');
        }else{

            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);
        }

    }


}