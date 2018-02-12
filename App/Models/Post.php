<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 29/01/2018
 * Time: 21:14
 */

namespace App\Models;

use PDO;

/*
 *  Post model
 *
 *  PHP version 7.2
 */

class Post extends \Core\Model
{
    /*
     *  Get all the posts as an associative array
     *
     *  @return array
     */
    public static function getAll()
    {

            $db = static::getDB();

            $stmt = $db->query('SELECT * FROM posts ORDER BY created_at');

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;

    }

    /**
     * @param $id
     * @return array
     * This model gets a single post
     */
    public static function getSinglePost($id)
    {

            $db = static::getDB();

            $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id");
            $stmt->execute(array(':id'=>$id));

            $results = $stmt->fetch();

            return $results;

    }
}