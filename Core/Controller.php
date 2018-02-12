<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 27/01/2018
 * Time: 22:13
 */

namespace Core;

use \App\Auth;
use \App\Flash;
/*
 * Base controller
 *
 * PHP version 7.2
 */

abstract class Controller
{
    /*
     *  Parameters from the matched route
     *  @var array
     */
    protected $route_params = [];

    /*
     *  Class constructor
     *
     *  @param array $route_params Parameters from the route
     *
     *  @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    public function __call($name, $arguments)
    {
        $method = $name.'Action';

        if(method_exists($this, $method)){
            if($this->before() !== false){
                call_user_func_array([$this, $method], $arguments);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller ".get_class($this));
        }
    }

    /*
     *  Before filter - called before an action method.
     *
     *  @return void
     */
    protected function before()
    {

    }

    /*
     *  After filter - called after an action method.
     *
     *  @return void
     */
    protected function after()
    {

    }

    /**
     *  Redirect to a different page
     *
     * @param string $url. Relative URL
     *
     * @return void
     */
    public function redirect($url)
    {
        header('Location: http://'.$_SERVER['HTTP_HOST'].$url, true, 303);
        exit;
    }

    /**
     * Require thar the user is logged in before granting acces to restricted pages
     *
     * @return string
     */
    public function requireLogin()
    {
        if(!Auth::getUser()){
            Flash::addMessage('Please login to access this page.', Flash::INFO);

            Auth::rememberRequestedPage();

            $this->redirect('/ace-mvc/public/login');
        }
    }
}