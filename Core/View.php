<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 28/01/2018
 * Time: 18:21
 */

namespace Core;

/*
 *  View
 *
 *  PHP version 7.2
 */
use App\Auth;
use App\Flash;

class View
{

    /*
     *  Render a view file
     *
     *  @param string $view The view file
     *
     *  @return void
     */
    public static function render($view, $args=[])
    {
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view"; // relative to core directory

        if(is_readable($file)){
            require $file;
        }else{
            throw new \Exception("$file not found");
        }
    }

    /*
     *  Render view template using Twig
     *
     *  @param string $template The template file
     *  @param array $args Associative array of data to display in the view (optional)
     *
     *  @return void
     */
    public static function renderTemplate($template, $args=[])
    {
        echo static::getTemplate($template, $args);
    }

    /*
     *  Get view template using Twig
     *
     *  @param string $template The template file
     *  @param array $args Associative array of data to display in the view (optional)
     *
     *  @return void
     */
    public static function getTemplate($template, $args=[])
    {
        static $twig = null;

        if ($twig === null){
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig_Environment($loader);
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
        }

        return $twig->render($template, $args);
    }

}