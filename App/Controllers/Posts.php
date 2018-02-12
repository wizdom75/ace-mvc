<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 27/01/2018
 * Time: 00:49
 */

namespace App\Controllers;

use \Core\View;
use App\Models\Post;

/*
 *  Posts
 *
 *  PHP version 7.2
 */

class Posts extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $posts = Post::getAll();

        View::renderTemplate('Posts/index.html', [
            'posts' => $posts
        ]);
    }

    /**
     * Show the add page
     *
     * @return void
     */
    public function addNewAction()
    {
        echo 'Hello from the addNew action in the Posts Controller';
    }

    /*
     *  Show the edit page
     *
     *  @return void
     */
    public function editAction()
    {
        echo 'Hello from the edit action in the Posts controller!';
        echo '<p>Route parameters : <pre>'.htmlspecialchars(print_r($this->route_params, true)).'</pre></p>';
    }

    /**
     *  Get single post
     *
     */
    public function singlePost()
    {
        $post = Post::getSinglePost($this->route_params['id']);

        View::renderTemplate('Posts/single_post.html', [
            'post' => $post]
        );
    }

}