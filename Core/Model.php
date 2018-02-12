<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 29/01/2018
 * Time: 21:49
 */

namespace Core;
use App\Config;
use PDO;

/*
 *  Base model
 *
 *  PHP version 7.2
 */


abstract class Model
{

    /*
     *  Get the PDO database connection
     *
     *  @return mixed
     */
    protected static function getDB()
    {
        $db = null; // declaring this property as static causes errors

        if($db === null) {

            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            // Throw an Exception when error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $db;
            }
        }
}