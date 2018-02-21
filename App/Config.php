<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 29/01/2018
 * Time: 22:12
 */

namespace App;

/**
 * Class Config
 * @package App
 * Application configuration
 *
 * PHP version 7.2
 */

class Config
{
    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'mvc_db';

    /**
     * Database username
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'password';

    /**
     *  Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     *  Secret key for hashing passwords
     * @var boolean
     */
    const SECRET_KEY = 'AeQZ3Oq6bpjUsgXwwc5av9CUALp0PWUC';

    /**
     *  Mailgun API key
     *
     * @var string
     */
    const MAILGUN_API_KEY = 'key-79156ea96c3c8b188aa6b8c8288a24a4';

    /**
     *  Mailgun domain
     *
     * @var string
     */
    const MAILGUN_DOMAIN = '';
}
