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
        View::renderTemplate('Home/index.html');
    }

}
