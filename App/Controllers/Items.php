<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 03/02/2018
 * Time: 23:24
 */

namespace App\Controllers;

use \Core\View;


/**
 * Class Items
 * @package App\Controllers
 *
 * PHP version 7.2
 */

class Items extends Authenticated
{

    /**
     * Items index method
     *
     * @return void
     */
    public function indexAction()
    {

        View::renderTemplate('Items/index.html');
    }

}