<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 04/02/2018
 * Time: 22:25
 */

namespace App;
/**
 * Class Flash Flash notification messages
 * @package App
 *
 * PHP version 7.2
 */

class Flash
{
    /**
     * Success message type
     * @var string
     */
    const SUCCESS = 'success';

    /**
     * Information message type
     * @var string
     */
    const INFO = 'info';

    /**
     * Warning message type
     * @var string
     */
    const WARNING = 'warning';

    /**
     * Add a message
     *
     * @param string $message The message content
     *
     * @return void
     */
    public static function addMessage($message, $type='success')
    {
        // Create and array in the session if it does not exist already.
        if(!isset($_SESSION['flash_notifications'])){
            $_SESSION['flash_notifications'] = [];
        }

        //Append message to this session array
        $_SESSION['flash_notifications'][] = [
            'body' =>$message,
            'type' => $type
        ];
    }

    /**
     *  Get all the flash messages
     *
     *  @param mixed An array with all the messages or null if none set
     *
     * @return void
     */
    public static function getMessages()
    {
        if(isset($_SESSION['flash_notifications'])){
            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);

            return $messages;
        }
    }
}