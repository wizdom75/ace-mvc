<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 02/02/2018
 * Time: 22:43
 */

namespace App\Controllers;

use \App\Models\User;

/**
 * Class Account
 * @package App\Controllers
 *
 * PHP version 7.2
 */

class Account extends \Core\Controller
{

    /**
     *  Check if email is unique using AJAX
     *
     * @return void
     */
    public function validateEmailAction()
    {
       $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);

       header('Content-Type: application/json');
       echo json_encode($is_valid);
    }

}