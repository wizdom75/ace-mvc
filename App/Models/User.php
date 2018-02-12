<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 01/02/2018
 * Time: 23:35
 */

namespace App\Models;

use \App\Mail;
use \App\Token;
use \Core\Model;
use \Core\View;

use PDO;

/**
 * Class User
 * @package App\Models
 *  PHP version 7.2
 */
class User extends Model
{
    /**
     *  Error messages
     *
     * @var array
     */
    public $errors = [];
    public $expiry_timestamp;
    public $remember_token;

    /**
     *  Class constructor
     *
     * @param array $data Initial property values
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     *  Save the user model with the current property values
     *
     * @return mixed
     */
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();

            $sql = 'INSERT INTO users (name, email, password_hash, activation_hash) VALUES (:name, :email, :password_hash, :activation_hash)';

            $db = static::getDB();

            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;

    }

    /**
     *  Validate current values, adding validation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        //name
        if ($this->name == '') {
            $this->errors[] = 'Name is required';
        }

        //email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Invalid email';
        }
        // call the validate email method
        if (static::emailExists($this->email, $this->id ?? null)){
            $this->errors[] = 'The email address you have chosen is already taken';
        }

        //Password
        if(isset($this->password)){

            if (strlen($this->password) < 6) {
                $this->errors[] = 'Your password must be at least 6 characters long.';
            }

            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password must include at least one letter';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password must contain at least one numeric character';
            }
        }

    }

    /**
     *  See if the email address supplied already exists in the database
     *
     * @param string $email address to search for
     * @param $ignore_id Return false if the record found has this ID
     *
     * @return boolean True is email exists, false otherwise
     */
    public static function emailExists($email, $ignore_id=null)
    {
        $user = static::findByEmail($email);

        if($user){
            if($user->id !=$ignore_id){
                return true;
            }
        }
        return false;
    }

    /**
     *  Find a user model by email address
     *
     * @param string $email address to search for
     *
     * @return mixed user object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     *  Authenticate a user by email and password.
     *
     *  @param string $email address
     *  @param string $password
     *
     *  @return mixed The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if($user && $user->is_active){
            if(password_verify($password, $user->password_hash)){
                return $user;
            }
        }

        return false;
    }

    /**
     *  Find a user model by ID
     *
     * @param string $id For the user ID
     *
     * @return mixed user object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     *  Remember the Login by inserting a new unique token into the remembered_logins table for this user record
     *
     * @return boolean True if logged in was remembered successfully, false otherwise.
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30; // 30 days from now
        $expires_at = date('Y-m-d H:i:s', $this->expiry_timestamp); // format the timestamp before binding to db params

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at) VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':expires_at',$expires_at, PDO::PARAM_STR);

        return $stmt->execute();


    }

    /**
     *  send password reset instructions to the user specified
     *
     * @param string $email The email address
     *
     * @return void
     */
    public static function sendPasswordReset($email)
    {
        $user = static::findByEmail($email);

        if($user){// Start the password reset process

            if($user->startPasswordReset()){
                // send reset email to user
                $user->sendPasswordResetEmail();
            }

        }
    }

    /**
     * Start the password reset process by generating a new token and expiry
     *
     * @return boolean
     */
    protected function startPasswordReset()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 + 60 + 2; // Expires 2 hours from now

        $expires_at = date('Y-m-d H:i:s', $expiry_timestamp);

        $sql =  'UPDATE users SET password_reset_hash = :token_hash,password_reset_expires_at = :expires_at 
                 WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', $expires_at, PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();

    }

    /**
     *  Send password reset instructions in an email to the user
     *
     * @return void
     */
    protected function sendPasswordResetEmail()
    {
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/ace-mvc/public/password/reset/'.$this->password_reset_token;

        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

        Mail::send($this->email, 'Password reset', $text, $html);

    }

    /**
     *  Find the user model by password reset token and expiry
     *
     * @param string $token Password reset token sent to the user
     *
     * @return mixed User object if found and also token has not expired, else null.
     */
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM users WHERE password_reset_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        if($user){
            if(strtotime($user->password_reset_expires_at)>time()){
                return $user;
            }
        }
    }

    /**
     *  Reset the password
     *
     * @param string $password The  new password
     *
     * @return boolean True if the password was updated successfully, false otherwise
     */
    public function resetPassword($password)
    {
        $this->password = $password;

        $this->validate();

        if(empty($this->errors)){

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users SET password_hash = :password_hash, password_reset_hash = NULL, password_reset_expires_at = NULL WHERE id =:id';

            $db = self::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            return $stmt->execute();

        }

        return false;
    }

    /**
     *  Send an email to the user containing the activation link
     *
     * @return void
     */
    public function sendActivationEmail()
    {
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/ace-mvc/public/signup/activate/'.$this->activation_token;

        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

        Mail::send($this->email, 'Account activation', $text, $html);

    }

    /**
     *  Activate the user account with the specified activation token
     *
     * @param string  $value Activation token from URL
     *
     * @return void
     */
    public static function activate($value)
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users SET is_active = 1, activation_hash = null WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Update the user's profile
     *
     * @param array $data Data from the edit profile form
     *
     * @return boolean True if the data was updated, false otherwise
     */
    public function updateProfile($data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];

        // Only validate and update the password if value is provided
        if($data['password'] !=''){
            $this->password = $data['password'];
        }


        $this->validate();

        if(empty($this->errors)){
            $sql = 'UPDATE users SET name = :name, email = :email';

            // Add password if it is set
            if(isset($this->password)){

                $sql .= ', password_hash = :password_hash';
            }

            $sql .= ' WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            // Add password if it is set
            if(isset($this->password)){

                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            }


            return $stmt->execute();

        }
        return false;

    }
}































