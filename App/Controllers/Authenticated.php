<?php

namespace App\Controllers;
use \Core\Controller;

/**
 * Class Authenticated
 * @package App\Controllers
 *
 * PHP version 7.2
 */
abstract class Authenticated extends Controller
{

    /**
     * Require the user to be authorised before giving access to all the methods in this controller
     *
     * @return void
     */
    protected function before()
    {
        $this->requireLogin();
    }

}