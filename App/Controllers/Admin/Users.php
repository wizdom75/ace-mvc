<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 28/01/2018
 * Time: 17:58
 */

namespace  App\Controllers\Admin;

/*
 * User admin Controller
 *
 * PHP version 7.2
 */


class Users extends \Core\Controller
{

    /*
     *  Before filter
     *
     *  @ return void
     */
    protected function before()
    {
        // Make sure the admin user is logged in other return false
        // to prevent non logged in users
        //return false;
    }

    /*
     *  Show the index page
     *
     *  @return void
     */
    public function indexAction()
    {
        echo 'User admin index!';
    }
}
