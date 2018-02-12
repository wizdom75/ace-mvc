<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 27/01/2018
 * Time: 21:37
 */

namespace App\Controllers;

use App\Auth;
use \Core\View;


class Home extends \Core\Controller
{
    public function indexAction()
    {


       // \App\Mail::send('pmn@outlook.com', 'Test', 'This is a test.', '<h1>This is a test.</h1>h1>');

        View::renderTemplate('Home/index.html');
    }

}